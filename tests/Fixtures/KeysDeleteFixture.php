<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class KeysDeleteFixture
{
    /**
     * Mirrors the 200 example for `DELETE /keys/{hash}`.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = ['deleted' => true];
}
