<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class GuardrailsRetrieveFixture
{
    /**
     * Mirrors the 200 example for `GET /guardrails/{id}`.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        'data' => [
            'allowed_models' => null,
            'allowed_providers' => ['openai', 'anthropic', 'google'],
            'created_at' => '2025-08-24T10:30:00Z',
            'description' => 'Guardrail for production environment',
            'enforce_zdr' => false,
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'ignored_providers' => null,
            'limit_usd' => 100,
            'name' => 'Production Guardrail',
            'reset_interval' => 'monthly',
            'updated_at' => '2025-08-24T15:45:00Z',
        ],
    ];
}
