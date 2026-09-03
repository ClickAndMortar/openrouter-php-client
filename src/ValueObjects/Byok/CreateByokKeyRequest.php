<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Byok;

use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * Typed builder for `POST /byok`.
 *
 * `$key` is the provider secret. It is sent once and never returned; the
 * stored credential only exposes a masked `label`.
 */
final class CreateByokKeyRequest
{
    /**
     * @param  list<string>|null  $allowedModels
     * @param  list<string>|null  $allowedApiKeyHashes
     * @param  list<string>|null  $allowedUserIds
     * @param  array<string, mixed>  $extras
     */
    public function __construct(
        public readonly string $provider,
        public readonly string $key,
        public readonly ?string $name = null,
        public readonly ?bool $disabled = null,
        public readonly ?bool $isFallback = null,
        public readonly ?array $allowedModels = null,
        public readonly ?array $allowedApiKeyHashes = null,
        public readonly ?array $allowedUserIds = null,
        public readonly array $extras = [],
    ) {
        if ($provider === '') {
            throw new InvalidArgumentException('CreateByokKeyRequest::$provider must not be empty');
        }

        if ($key === '') {
            throw new InvalidArgumentException('CreateByokKeyRequest::$key must not be empty');
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['provider' => $this->provider, 'key' => $this->key];

        foreach ([
            'name' => $this->name,
            'disabled' => $this->disabled,
            'is_fallback' => $this->isFallback,
            'allowed_models' => $this->allowedModels,
            'allowed_api_key_hashes' => $this->allowedApiKeyHashes,
            'allowed_user_ids' => $this->allowedUserIds,
        ] as $wire => $value) {
            if ($value !== null) {
                $data[$wire] = $value;
            }
        }

        return [...$data, ...$this->extras];
    }
}
