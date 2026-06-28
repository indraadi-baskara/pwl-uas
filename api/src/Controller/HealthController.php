<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\HealthResponse;
use App\Http\Request;
use App\Http\Response;
use OpenApi\Attributes as OA;

final class HealthController
{
    #[OA\Get(
        path: '/health',
        operationId: 'getHealth',
        summary: 'Check API health',
        responses: [
            new OA\Response(
                response: 200,
                description: 'API is healthy',
                content: new OA\JsonContent(ref: '#/components/schemas/HealthResponse'),
            ),
        ],
    )]
    public function index(Request $request): never
    {
        Response::json((new HealthResponse(status: 'ok'))->toArray());
    }
}
