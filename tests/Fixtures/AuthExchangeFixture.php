<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class AuthExchangeFixture
{
    /**
     * Mirrors the 200 example for `POST /auth/keys`.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        'key' => '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',
        'user_id' => 'user_2yOPcMpKoQhcd4bVgSMlELRaIah',
    ];
}
