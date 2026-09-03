<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Scim;

/**
 * A group synchronised into OpenRouter by your identity provider over SCIM.
 * Groups are read-only here; membership is managed upstream in the IdP.
 */
final class ScimGroup
{
    /**
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly string $id,
        public readonly ?string $organizationId,
        public readonly ?string $displayName,
        public readonly ?string $externalId,
        public readonly ?string $createdAt,
        public readonly ?string $updatedAt,
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
            'organization_id',
            'display_name',
            'external_id',
            'created_at',
            'updated_at',
        ]));

        return new self(
            id: is_string($attributes['id'] ?? null) ? $attributes['id'] : '',
            organizationId: is_string($attributes['organization_id'] ?? null) ? $attributes['organization_id'] : null,
            displayName: is_string($attributes['display_name'] ?? null) ? $attributes['display_name'] : null,
            externalId: is_string($attributes['external_id'] ?? null) ? $attributes['external_id'] : null,
            createdAt: is_string($attributes['created_at'] ?? null) ? $attributes['created_at'] : null,
            updatedAt: is_string($attributes['updated_at'] ?? null) ? $attributes['updated_at'] : null,
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
            'organization_id' => $this->organizationId,
            'display_name' => $this->displayName,
            'external_id' => $this->externalId,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return [...$data, ...$this->extras];
    }
}
