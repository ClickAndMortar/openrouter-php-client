<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Byok;

/**
 * Typed builder for `PATCH /byok/{id}`. Every field is optional; only the ones
 * you set are sent. Pass `$key` to rotate the provider secret.
 */
final class UpdateByokKeyRequest
{
    /**
     * @param  list<string>|null  $allowedModels
     * @param  list<string>|null  $allowedApiKeyHashes
     * @param  list<string>|null  $allowedUserIds
     * @param  array<string, mixed>  $extras
     */
    public function __construct(
        public readonly ?string $key = null,
        public readonly ?string $name = null,
        public readonly ?bool $disabled = null,
        public readonly ?bool $isFallback = null,
        public readonly ?array $allowedModels = null,
        public readonly ?array $allowedApiKeyHashes = null,
        public readonly ?array $allowedUserIds = null,
        public readonly array $extras = [],
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];

        foreach ([
            'key' => $this->key,
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
