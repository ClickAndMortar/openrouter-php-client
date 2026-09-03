<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class PresetVersionsFixture
{
    /**
     * Mirrors the `PresetVersionsResponse` shape from openapi-openrouter.yaml.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        'data' => [
            [
                'id' => 'pv_01HQ8Z3K4M5N6P7Q8R9S',
                'preset_id' => 'preset_01HQ8Z3K4M5N6P7Q8R9S',
                'version' => 3,
                'creator_id' => 'user_01HQ8Z3K4M5N6P7Q8R9S',
                'system_prompt' => 'You are a support agent.',
                'config' => ['model' => 'openai/gpt-4o', 'temperature' => 0.2],
                'created_at' => '2026-01-04T10:00:00Z',
                'updated_at' => '2026-02-01T09:30:00Z',
            ],
        ],
        'total_count' => 1,
    ];
}
