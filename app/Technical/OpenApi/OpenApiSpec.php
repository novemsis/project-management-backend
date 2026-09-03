<?php

namespace App\Technical\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    description: 'API documentation',
    title: 'VisionFlow'
)]
#[OA\Server(
    url: '/api',
    description: 'app backend'
)]
class OpenApiSpec
{
}
