<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class WorkspaceBudgetsFixture
{
    /**
     * Mirrors the `WorkspaceBudgetsResponse` shape from openapi-openrouter.yaml.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        'data' => [
            [
                'id' => 'wb_01HQ8Z3K4M5N6P7Q8R9S',
                'workspace_id' => 'ws_01HQ8Z3K4M5N6P7Q8R9S',
                'limit_usd' => 250.0,
                'reset_interval' => 'monthly',
                'created_at' => '2026-01-04T10:00:00Z',
                'updated_at' => '2026-02-01T09:30:00Z',
            ],
        ],
        'include_byok_in_budgets' => true,
    ];
}
