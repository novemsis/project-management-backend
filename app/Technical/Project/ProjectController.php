<?php

namespace App\Technical\Project;

use App\Domain\Decision\DecisionService;
use App\Domain\Plan\PlanService;
use App\Domain\Project\Project;
use App\Domain\Project\ProjectService;
use App\Domain\Target\TargetService;
use App\Domain\ToDo\ToDo;
use App\Domain\ToDo\ToDoService;
use App\Global\DatePeriod;
use App\Technical\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;
use Throwable;

class ProjectController extends Controller
{
    public function __construct(
        private readonly ProjectService $projectService,
        private readonly TargetService $targetService,
        private readonly PlanService $planService,
        private readonly DecisionService $decisionService,
        private readonly ToDoService $toDoService
    ) {
    }

    /** @throws Throwable */
    #[OA\POST(
        path: '/project/create',
        summary: 'create a new project',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['title'],
                properties: [
                    new OA\Property(property: 'title', type: 'string', example: 'Mein Projekt'),
                    new OA\Property(property: 'project_start', description: 'Date format: YYYY-mm-dd', type: 'string', example: '2026-01-31'),
                    new OA\Property(property: 'project_end', description: 'Date format: YYYY-mm-dd', type: 'string', example: '2026-01-31'),
                    new OA\Property(property: 'check_period', description: 'Date Period', type: DatePeriod::class, example: 'p1d'),
                ]
            )
        ),
        tags: ['Projects'],
        responses: [new OA\Response(response: 204, description: 'Project created')]
    )]
    public function createProject(Request $request, CreateProjectDto $projectDto): JsonResponse
    {
        $user = $request->user();
        $project = new Project([
            'user_id' => $user->id,
            'title' => $projectDto->getTitle(),
            'description' => $projectDto->getDescription(),
            'project_start' => $projectDto->getProjectStart(),
            'project_end' => $projectDto->getProjectEnd(),
            'check_period' => $projectDto->getCheckPeriod(),
        ]);
        DB::transaction(function() use ($user, $projectDto, $project) {
            $project->save();

            $this->targetService->createTarget($project, $projectDto->getTargetDefinition());
            $this->planService->createPlan(
                $project,
                $projectDto->getPlanStrategicDefinition(),
                $projectDto->getPlanTacticalDefinition(),
                $projectDto->getPlanOperationalDefinition()
            );
            $this->decisionService->createDecision(
                $project,$projectDto->getDecisionCarryThrough(),
                $projectDto->getDecisionDescription()
            );
            $this->toDoService->createToDosFromArray(
                $project,
                array_map(fn($todo) => $todo->description, $projectDto->getToDos())
            );
        });

        return response()->json([
            'id' => $project->id,
        ]);
    }

    public function getProjects(Request $request): Response|JsonResponse
    {
        $user = $request->user();
        $projects = $this->projectService->getProjects($user);

        $result = [];
        foreach ($projects as $project) {
            $result[$project->id] = [
                'id' => $project->id,
                'title' => $project->title,
                'description' => $project->description,
                'project_start' => $project->project_start,
                'project_end' => $project->project_end,
                'created_at' => $project->created_at,
            ];

            $nextCheck = $this->getNextCheck($project);
            if (null !== $nextCheck) {
                $result[$project->id]['next_check'] = $nextCheck;
            }

            $currentTarget = $this->getCurrentTarget($project);
            if (null !== $currentTarget) {
                $result[$project->id]['target'] = $currentTarget;
            }

            $currentPlan = $this->getCurrentPlan($project);
            if (null !== $currentPlan) {
                $result[$project->id]['plan'] = $currentPlan;
            }

            $currentDecision = $this->getCurrentDecision($project);
            if (null !== $currentDecision) {
                $result[$project->id]['decision'] = $currentDecision;
            }

            $currentToDos = $this->getCurrentToDos($project);
            if (null !== $currentToDos) {
                $result[$project->id]['to_dos'] = $currentToDos;
                $result[$project->id]['done_to_dos_ratio'] = $this->getToDosDoneRatio($currentToDos);
            }
        }

        return response()->json($result);
    }

    public function getProject(Request $request, String $projectId): Response|JsonResponse
    {
        $user = $request->user();
        $project = $this->projectService->getProject($user, $projectId);
        if (null === $project) {
            return response(content: 'project not found', status: 404);
        }

        $result = [
            'id' => $project->id,
            'title' => $project->title,
            'description' => $project->description,
            'project_start' => $project->project_start,
            'project_end' => $project->project_end,
            'created_at' => $project->created_at,
        ];

        $nextCheck = $this->getNextCheck($project);
        if (null !== $nextCheck) {
            $result['next_check'] = $nextCheck;
        }

        $currentTarget = $this->getCurrentTarget($project);
        if (null !== $currentTarget) {
            $result['target'] = $currentTarget;
        }

        $currentPlan = $this->getCurrentPlan($project);
        if (null !== $currentPlan) {
            $result['plan'] = $currentPlan;
        }

        $currentDecision = $this->getCurrentDecision($project);
        if (null !== $currentDecision) {
            $result['decision'] = $currentDecision;
        }

        $currentToDos = $this->getCurrentToDos($project);
        if (null !== $currentToDos) {
            $result['to_dos'] = $currentToDos;
            $result['done_to_dos_ratio'] = $this->getToDosDoneRatio($currentToDos);
        }

        return response()->json($result);
    }

    private function getNextCheck(Project $project): ?Carbon {
        $nextCheck = null;
        if (null !== $project->check_period) {
            $nextCheck = $project->created_at->add($project->check_period->toCarbonInterval());
            $latestPerformedCheck = $project->latestPerformedCheck();
            if (null !== $latestPerformedCheck) {
                $nextCheck = $latestPerformedCheck->created_at->add($project->check_period->toCarbonInterval());
            } elseif (null !== $project->project_start) {
                $nextCheck = $project->project_start->add($project->check_period->toCarbonInterval());
            }
        }

        return $nextCheck;
    }

    /** @return array<string, string|bool|Carbon>|null */
    private function getCurrentTarget(Project $project): ?array {
        $currentTarget = $project->currentTarget();
        if (null !== $currentTarget) {
            return [
                'id' => $currentTarget->id,
                'definition' => $currentTarget->definition,
                'is_smarter' => $this->targetService->isTargetSmarter($currentTarget),
                'smarter_details' => [
                    'is_spezifisch' => $currentTarget->smarter_spezifisch,
                    'is_messbar' => $currentTarget->smarter_messbar,
                    'is_ambitioniert' => $currentTarget->smarter_ambitioniert,
                    'is_realistisch' => $currentTarget->smarter_realistisch,
                    'is_terminiert' => $currentTarget->smarter_terminiert,
                    'is_emotionalisiert' => $currentTarget->smarter_emotionalisiert,
                    'is_ressourceneinsetzend' => $currentTarget->smarter_ressourceneinsatz,
                ],
                'created_at' => $currentTarget->created_at,
            ];
        }

        return null;
    }

    /** @return array<string, string|bool|Carbon>|null */
    private function getCurrentPlan(Project $project): ?array {
        $currentPlan = $project->currentPlan();
        if (null !== $currentPlan) {
            return [
                'id' => $currentPlan->id,
                'is_defined' => $this->planService->isPlanDefined($currentPlan),
                'definition_details' => [
                    'strategic' => $currentPlan->strategic_definition,
                    'tactical' => $currentPlan->tactical_definition,
                    'operational' => $currentPlan->operational_definition,
                ],
                'created_at' => $currentPlan->created_at,
            ];
        }

        return null;
    }

    private function getCurrentDecision(Project $project): ?array {
        $currentDecision = $project->currentDecision();
        if (null !== $currentDecision) {
            return [
                'id' => $currentDecision->id,
                'carry_through' => $currentDecision->carry_through,
                'description' => $currentDecision->description,
                'created_at' => $currentDecision->created_at,
            ];
        }

        return null;
    }

    private function getCurrentToDos(Project $project): ?array {
        $currentToDos = $project->currentToDos();
        if ($currentToDos->isNotEmpty()) {
            $toDos = [];
            /** @var ToDo $toDo */
            foreach ($currentToDos as $toDo) {
                $toDos[] = [
                    'id' => $toDo->id,
                    'description' => $toDo->description,
                    'done' => $toDo->done,
                    'created_at' => $toDo->created_at,
                ];
            }

            return $toDos;
        }

        return null;
    }

    /** @param ToDo[] $toDos */
    private function getToDosDoneRatio(array $toDos): string {
        $doneCount = 0;
        foreach ($toDos as $toDo) {
            $toDo['done'] && $doneCount++;
        }

        return (string) ($doneCount/count($toDos));
    }
}
