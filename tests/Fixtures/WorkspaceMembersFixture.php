<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class WorkspaceMembersFixture
{
    /**
     * Mirrors the `WorkspaceMembersResponse` shape from openapi-openrouter.yaml.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        'data' => [
            [
                'id' => 'wm_01HQ8Z3K4M5N6P7Q8R9S',
                'workspace_id' => 'ws_01HQ8Z3K4M5N6P7Q8R9S',
                'user_id' => 'user_01HQ8Z3K4M5N6P7Q8R9S',
                'role' => 'admin',
                'created_at' => '2026-01-04T10:00:00Z',
            ],
        ],
        'total_count' => 1,
    ];
}
