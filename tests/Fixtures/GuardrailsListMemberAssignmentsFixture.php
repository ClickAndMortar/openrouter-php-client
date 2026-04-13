<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class GuardrailsListMemberAssignmentsFixture
{
    /**
     * Mirrors the 200 example for `GET /guardrails/{id}/assignments/members` and
     * `GET /guardrails/assignments/members`.
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
                'organization_id' => 'org_xyz789',
                'user_id' => 'user_abc123',
            ],
        ],
        'total_count' => 1,
    ];
}
