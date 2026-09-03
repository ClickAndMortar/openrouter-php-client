<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Byok;

/**
 * A provider credential you supplied, used instead of OpenRouter's own key
 * for that provider. The secret itself is never returned; `label` is a masked
 * hint, and the allow-lists scope which models, API keys and users may spend
 * against it.
 */
final class ByokKey
{
    /**
     * @param  list<string>|null  $allowedModels
     * @param  list<string>|null  $allowedApiKeyHashes
     * @param  list<string>|null  $allowedUserIds
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly string $id,
        public readonly string $provider,
        public readonly ?string $workspaceId,
        public readonly ?string $label,
        public readonly ?string $name,
        public readonly ?bool $disabled,
        public readonly ?bool $isFallback,
        public readonly ?array $allowedModels,
        public readonly ?array $allowedApiKeyHashes,
        public readonly ?array $allowedUserIds,
        public readonly ?int $sortOrder,
        public readonly ?string $createdAt,
        public readonly array $extras = [],
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $extras = array_diff_key($attributes, array_flip([
            'id',
            'provider',
            'workspace_id',
            'label',
            'name',
            'disabled',
            'is_fallback',
            'allowed_models',
            'allowed_api_key_hashes',
            'allowed_user_ids',
            'sort_order',
            'created_at',
        ]));

        return new self(
            id: is_string($attributes['id'] ?? null) ? $attributes['id'] : '',
            provider: is_string($attributes['provider'] ?? null) ? $attributes['provider'] : '',
            workspaceId: is_string($attributes['workspace_id'] ?? null) ? $attributes['workspace_id'] : null,
            label: is_string($attributes['label'] ?? null) ? $attributes['label'] : null,
            name: is_string($attributes['name'] ?? null) ? $attributes['name'] : null,
            disabled: isset($attributes['disabled']) ? (bool) $attributes['disabled'] : null,
            isFallback: isset($attributes['is_fallback']) ? (bool) $attributes['is_fallback'] : null,
            allowedModels: is_array($attributes['allowed_models'] ?? null)
                ? array_values(array_map('strval', $attributes['allowed_models']))
                : null,
            allowedApiKeyHashes: is_array($attributes['allowed_api_key_hashes'] ?? null)
                ? array_values(array_map('strval', $attributes['allowed_api_key_hashes']))
                : null,
            allowedUserIds: is_array($attributes['allowed_user_ids'] ?? null)
                ? array_values(array_map('strval', $attributes['allowed_user_ids']))
                : null,
            sortOrder: is_int($attributes['sort_order'] ?? null) ? $attributes['sort_order'] : null,
            createdAt: is_string($attributes['created_at'] ?? null) ? $attributes['created_at'] : null,
            extras: $extras,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];

        foreach ([
            'id' => $this->id,
            'provider' => $this->provider,
            'workspace_id' => $this->workspaceId,
            'label' => $this->label,
            'name' => $this->name,
            'disabled' => $this->disabled,
            'is_fallback' => $this->isFallback,
            'allowed_models' => $this->allowedModels,
            'allowed_api_key_hashes' => $this->allowedApiKeyHashes,
            'allowed_user_ids' => $this->allowedUserIds,
            'sort_order' => $this->sortOrder,
            'created_at' => $this->createdAt,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return [...$data, ...$this->extras];
    }
}
