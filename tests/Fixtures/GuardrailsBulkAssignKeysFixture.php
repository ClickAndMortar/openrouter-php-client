<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class GuardrailsBulkAssignKeysFixture
{
    /**
     * Mirrors the 200 example for `POST /guardrails/{id}/assignments/keys`.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        'assigned_count' => 3,
    ];
}
