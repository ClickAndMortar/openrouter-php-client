<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class GuardrailsCreateFixture
{
    /**
     * Mirrors the 201 example for `POST /guardrails`.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        'data' => [
            'allowed_models' => null,
            'allowed_providers' => ['openai', 'anthropic', 'google'],
            'created_at' => '2025-08-24T10:30:00Z',
            'description' => 'A guardrail for limiting API usage',
            'enforce_zdr' => false,
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'ignored_providers' => null,
            'limit_usd' => 50,
            'name' => 'My New Guardrail',
            'reset_interval' => 'monthly',
            'updated_at' => null,
        ],
    ];
}
