<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class SwaggerController extends Controller
{
    /**
     * OpenAPI 3.0 spec for Swagger UI.
     */
    public function spec(): JsonResponse
    {
        $baseUrl = config('app.url');

        $spec = [
            'openapi' => '3.0.3',
            'info' => [
                'title' => config('app.name').' API',
                'description' => 'API documentation',
                'version' => '1.0.0',
            ],
            'servers' => [
                ['url' => $baseUrl, 'description' => 'Current'],
            ],
            'paths' => [
                '/api/user' => [
                    'get' => [
                        'summary' => 'Current user',
                        'description' => 'Returns the authenticated user (Sanctum).',
                        'security' => [['sanctum' => []]],
                        'responses' => [
                            '200' => [
                                'description' => 'Authenticated user',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'id' => ['type' => 'integer', 'example' => 1],
                                                'name' => ['type' => 'string'],
                                                'email' => ['type' => 'string', 'format' => 'email'],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            '401' => ['description' => 'Unauthenticated'],
                        ],
                    ],
                ],
            ],
            'components' => [
                'securitySchemes' => [
                    'sanctum' => [
                        'type' => 'apiKey',
                        'in' => 'header',
                        'name' => 'Authorization',
                        'description' => 'Bearer token (Laravel Sanctum)',
                    ],
                ],
            ],
        ];

        return response()->json($spec);
    }
}
