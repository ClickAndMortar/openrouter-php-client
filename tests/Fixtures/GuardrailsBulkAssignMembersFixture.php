<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class GuardrailsBulkAssignMembersFixture
{
    /**
     * Mirrors the 200 example for `POST /guardrails/{id}/assignments/members`.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        'assigned_count' => 2,
    ];
}
