<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class GuardrailsBulkUnassignKeysFixture
{
    /**
     * Mirrors the 200 example for `POST /guardrails/{id}/assignments/keys/remove`.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        'unassigned_count' => 3,
    ];
}
