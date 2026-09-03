<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class PresetRetrieveFixture
{
    /**
     * Mirrors the `PresetResponse` shape from openapi-openrouter.yaml.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        'data' => [
            'id' => 'preset_01HQ8Z3K4M5N6P7Q8R9S',
            'name' => 'Support agent',
            'slug' => 'support-agent',
            'description' => 'Tuned for first-line support replies.',
            'status' => 'active',
            'creator_user_id' => 'user_01HQ8Z3K4M5N6P7Q8R9S',
            'workspace_id' => 'ws_01HQ8Z3K4M5N6P7Q8R9S',
            'designated_version_id' => 'pv_01HQ8Z3K4M5N6P7Q8R9S',
            'created_at' => '2026-01-04T10:00:00Z',
            'updated_at' => '2026-02-01T09:30:00Z',
            'status_updated_at' => '2026-02-01T09:30:00Z',
    ],
    ];
}
