<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class WorkspacesListFixture
{
    /**
     * Mirrors the `WorkspacesListResponse` shape from openapi-openrouter.yaml.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        'data' => [
    [
                'id' => 'ws_01HQ8Z3K4M5N6P7Q8R9S',
                'name' => 'Platform team',
                'slug' => 'platform-team',
                'description' => 'Shared workspace for the platform team.',
                'default_guardrail_id' => 'gr_01HQ8Z3K4M5N6P7Q8R9S',
                'default_text_model' => 'openai/gpt-4o',
                'default_image_model' => null,
                'default_provider_sort' => 'throughput',
                'is_observability_io_logging_enabled' => true,
                'is_observability_broadcast_enabled' => false,
                'is_data_discount_logging_enabled' => false,
                'io_logging_sampling_rate' => 0.25,
                'io_logging_api_key_ids' => ['key_1'],
                'include_byok_in_budgets' => true,
                'created_at' => '2026-01-04T10:00:00Z',
                'updated_at' => '2026-02-01T09:30:00Z',
                'created_by' => 'user_01HQ8Z3K4M5N6P7Q8R9S',
            ],
        ],
        'total_count' => 1,
    ];
}
