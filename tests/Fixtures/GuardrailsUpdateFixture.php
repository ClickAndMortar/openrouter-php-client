<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class GuardrailsUpdateFixture
{
    /**
     * Mirrors the 200 example for `PATCH /guardrails/{id}`.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        'data' => [
            'allowed_models' => null,
            'allowed_providers' => ['openai'],
            'created_at' => '2025-08-24T10:30:00Z',
            'description' => 'Updated description',
            'enforce_zdr' => true,
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'ignored_providers' => null,
            'limit_usd' => 75,
            'name' => 'Updated Guardrail Name',
            'reset_interval' => 'weekly',
            'updated_at' => '2025-08-24T16:00:00Z',
        ],
    ];
}
