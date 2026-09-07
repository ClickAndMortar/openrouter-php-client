<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class OauthFixture
{
    /**
     * Mirrors the 200 example for `GET /oauth/jwks`.
     *
     * @var array<string, mixed>
     */
    public const JWKS = [
        'keys' => [
            [
                'kty' => 'EC',
                'crv' => 'P-256',
                'kid' => 'or-2026-09',
                'x' => 'f83OJ3D2xF1Bg8vub9tLe1gHMzV76e8Tus9uPHvRVEU',
                'y' => 'x_FEzRu9m36HLN_tue659LNpXW6pCyStikYjKIWI5a0',
                'alg' => 'ES256',
                'use' => 'sig',
            ],
        ],
    ];

    /**
     * Mirrors the 200 example for `POST /oauth/token`.
     *
     * @var array<string, mixed>
     */
    public const TOKEN_EXCHANGE = [
        'access_token' => '<short-lived openrouter access token jwt>',
        'issued_token_type' => 'urn:ietf:params:oauth:token-type:access_token',
        'token_type' => 'Bearer',
        'expires_in' => 900,
        'scope' => 'inference',
    ];
}
