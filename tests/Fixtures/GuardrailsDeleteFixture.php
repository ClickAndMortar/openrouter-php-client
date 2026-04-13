<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class GuardrailsDeleteFixture
{
    /**
     * Mirrors the 200 example for `DELETE /guardrails/{id}`.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        'deleted' => true,
    ];
}
