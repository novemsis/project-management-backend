<?php

namespace App\Technical\Project;

use App\Global\DatePeriod;
use App\Technical\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class ProjectController extends Controller
{
    #[OA\POST(
        path: '/project/create',
        operationId: 'getProjects',
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
    public function createProject(CreateProjectDto $dto): Response {
        return response()->noContent();
    }
}
