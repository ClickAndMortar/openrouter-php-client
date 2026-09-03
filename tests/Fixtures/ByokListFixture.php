<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class ByokListFixture
{
    /**
     * Mirrors the `BYOKKey` list shape from openapi-openrouter.yaml.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        'data' => [
            [
                'id' => 'byok_01HQ8Z3K4M5N6P7Q8R9S',
                'provider' => 'anthropic',
                'workspace_id' => 'ws_01HQ8Z3K4M5N6P7Q8R9S',
                'label' => 'sk-ant-...4f2a',
                'name' => 'Anthropic production',
                'disabled' => false,
                'is_fallback' => true,
                'allowed_models' => ['anthropic/claude-sonnet-4'],
                'allowed_api_key_hashes' => ['abc123'],
                'allowed_user_ids' => null,
                'sort_order' => 1,
                'created_at' => '2026-01-04T10:00:00Z',
            ],
        ],
        'total_count' => 1,
    ];
}
