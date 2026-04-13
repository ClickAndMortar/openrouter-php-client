<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Auth;

use OpenRouter\Enums\Auth\CodeChallengeMethod;
use OpenRouter\Enums\Keys\LimitReset;
use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * Typed builder for `POST /auth/keys/code`. Creates an authorization code for
 * the PKCE flow.
 */
final class CreateAuthCodeRequest
{
    /**
     * @param  array<string, mixed>  $extras
     */
    public function __construct(
        public readonly string $callbackUrl,
        public readonly string $codeChallenge,
        public readonly CodeChallengeMethod|string $codeChallengeMethod,
        public readonly ?float $limit = null,
        public readonly LimitReset|string|null $usageLimitType = null,
        public readonly ?string $expiresAt = null,
        public readonly ?string $keyLabel = null,
        public readonly ?string $spawnAgent = null,
        public readonly ?string $spawnCloud = null,
        public readonly array $extras = [],
    ) {
        if ($this->callbackUrl === '') {
            throw new InvalidArgumentException('CreateAuthCodeRequest::$callbackUrl must not be an empty string');
        }

        if ($this->codeChallenge === '') {
            throw new InvalidArgumentException('CreateAuthCodeRequest::$codeChallenge must not be an empty string');
        }

        if ($this->keyLabel !== null && strlen($this->keyLabel) > 100) {
            throw new InvalidArgumentException('CreateAuthCodeRequest::$keyLabel must not exceed 100 characters');
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'callback_url' => $this->callbackUrl,
            'code_challenge' => $this->codeChallenge,
            'code_challenge_method' => $this->codeChallengeMethod instanceof CodeChallengeMethod
                ? $this->codeChallengeMethod->value
                : $this->codeChallengeMethod,
        ];

        if ($this->limit !== null) {
            $data['limit'] = $this->limit;
        }

        if ($this->usageLimitType !== null) {
            $data['usage_limit_type'] = $this->usageLimitType instanceof LimitReset
                ? $this->usageLimitType->value
                : $this->usageLimitType;
        }

        if ($this->expiresAt !== null) {
            $data['expires_at'] = $this->expiresAt;
        }

        if ($this->keyLabel !== null) {
            $data['key_label'] = $this->keyLabel;
        }

        if ($this->spawnAgent !== null) {
            $data['spawn_agent'] = $this->spawnAgent;
        }

        if ($this->spawnCloud !== null) {
            $data['spawn_cloud'] = $this->spawnCloud;
        }

        foreach ($this->extras as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }
}
