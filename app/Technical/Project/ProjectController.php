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
    public function createProject(Request $request, CreateProjectDto $projectDto): Response {
        $user = $request->user();
        DB::transaction(function() use ($user, $projectDto) {
            $project = new Project([
                'user_id' => $user->id,
                'title' => $projectDto->getTitle(),
                'project_start' => $projectDto->getProjectStart(),
                'project_end' => $projectDto->getProjectEnd(),
                'check_period' => $projectDto->getCheckPeriod(),
            ]);
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

        return response()->noContent();
    }

    public function getProjects(Request $request): Response|JsonResponse
    {
        $user = $request->user();
        $projects = $this->projectService->getProjects($user);

        $result = [];
        foreach ($projects as $project) {
            $currentTarget = $project->currentTarget();
            $currentPlan = $project->currentPlan();
            $currentDecision = $project->currentDecision();
            $currentToDos = $project->currentToDos();
            $result[$project->id] = [
                'id' => $project->id,
                'title' => $project->title,
                'project_start' => $project->project_start,
                'project_end' => $project->project_end,
                'created_at' => $project->created_at,
            ];

            if (null !== $project->check_period) {
                $nextCheck = $project->created_at->add($project->check_period->toCarbonInterval());
                $latestPerformedCheck = $project->latestPerformedCheck();
                if (null !== $latestPerformedCheck) {
                    $nextCheck = $latestPerformedCheck->created_at->add($project->check_period->toCarbonInterval());
                } elseif (null !== $project->project_start) {
                    $nextCheck = $project->project_start->add($project->check_period->toCarbonInterval());
                }

                $result[$project->id]['next_check'] = $nextCheck;
            }

            if (null !== $currentTarget) {
                $result[$project->id]['target'] = [
                    'id' => $currentTarget->id,
                    'definition' => $currentTarget->definition,
                    'is_smarter' => $this->targetService->isTargetSmarter($currentTarget),
                    'created_at' => $currentTarget->created_at,
                ];
            }

            if (null !== $currentPlan) {
                $result[$project->id]['plan'] = [
                    'id' => $currentPlan->id,
                    'is_defined' => $this->planService->isPlanDefined($currentPlan),
                    'created_at' => $currentPlan->created_at,
                ];
            }

            if (null !== $currentDecision) {
                $result[$project->id]['decision'] = [
                    'id' => $currentDecision->id,
                    'carry_through' => $currentDecision->carry_through,
                    'description' => $currentDecision->description,
                    'created_at' => $currentDecision->created_at,
                ];
            }

            if ($currentToDos->isNotEmpty()) {
                $toDos = [];
                $doneCount = 0;
                /** @var ToDo $toDo */
                foreach ($currentToDos as $toDo) {
                    $toDos[] = [
                        'id' => $toDo->id,
                        'description' => $toDo->description,
                        'done' => $toDo->done,
                        'created_at' => $toDo->created_at,
                    ];
                    $toDo->done && $doneCount++;
                }

                $result[$project->id]['to_dos'] = $toDos;
                $result[$project->id]['done_to_dos_ratio'] = $doneCount/$currentToDos->count();
            }
        }

        return response()->json($result);
    }
}
