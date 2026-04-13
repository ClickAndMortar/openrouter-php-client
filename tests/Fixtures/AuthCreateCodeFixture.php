<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class AuthCreateCodeFixture
{
    /**
     * Mirrors the 200 example for `POST /auth/keys/code`.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        'data' => [
            'app_id' => 12345,
            'created_at' => '2025-08-24T10:30:00Z',
            'id' => 'auth_code_xyz789',
        ],
    ];
}
