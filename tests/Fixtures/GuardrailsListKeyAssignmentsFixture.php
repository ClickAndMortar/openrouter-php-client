<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class GuardrailsListKeyAssignmentsFixture
{
    /**
     * Mirrors the 200 example for `GET /guardrails/{id}/assignments/keys` and
     * `GET /guardrails/assignments/keys`.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        'data' => [
            [
                'assigned_by' => 'user_abc123',
                'created_at' => '2025-08-24T10:30:00Z',
                'guardrail_id' => '550e8400-e29b-41d4-a716-446655440001',
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'key_hash' => 'c56454edb818d6b14bc0d61c46025f1450b0f4012d12304ab40aacb519fcbc93',
                'key_label' => 'prod-key',
                'key_name' => 'Production Key',
            ],
        ],
        'total_count' => 1,
    ];
}
