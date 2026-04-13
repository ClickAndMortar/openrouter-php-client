<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Auth;

use OpenRouter\Enums\Auth\CodeChallengeMethod;
use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * Typed builder for `POST /auth/keys`. Exchanges an authorization code from
 * the PKCE flow for a user-controlled API key.
 */
final class ExchangeCodeRequest
{
    /**
     * @param  array<string, mixed>  $extras
     */
    public function __construct(
        public readonly string $code,
        public readonly ?string $codeVerifier = null,
        public readonly CodeChallengeMethod|string|null $codeChallengeMethod = null,
        public readonly array $extras = [],
    ) {
        if ($this->code === '') {
            throw new InvalidArgumentException('ExchangeCodeRequest::$code must not be an empty string');
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['code' => $this->code];

        if ($this->codeVerifier !== null) {
            $data['code_verifier'] = $this->codeVerifier;
        }

        if ($this->codeChallengeMethod !== null) {
            $data['code_challenge_method'] = $this->codeChallengeMethod instanceof CodeChallengeMethod
                ? $this->codeChallengeMethod->value
                : $this->codeChallengeMethod;
        }

        foreach ($this->extras as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }
}
