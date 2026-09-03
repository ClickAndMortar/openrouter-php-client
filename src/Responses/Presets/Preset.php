<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Presets;

/**
 * A saved inference configuration addressed by slug. Sending a request to
 * `/presets/{slug}/...` records that request's settings as a new version.
 */
final class Preset
{
    /**
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $description,
        public readonly ?string $status,
        public readonly ?string $creatorUserId,
        public readonly ?string $workspaceId,
        public readonly ?string $designatedVersionId,
        public readonly ?string $createdAt,
        public readonly ?string $updatedAt,
        public readonly ?string $statusUpdatedAt,
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
            'name',
            'slug',
            'description',
            'status',
            'creator_user_id',
            'workspace_id',
            'designated_version_id',
            'created_at',
            'updated_at',
            'status_updated_at',
        ]));

        return new self(
            id: is_string($attributes['id'] ?? null) ? $attributes['id'] : '',
            name: is_string($attributes['name'] ?? null) ? $attributes['name'] : '',
            slug: is_string($attributes['slug'] ?? null) ? $attributes['slug'] : '',
            description: is_string($attributes['description'] ?? null) ? $attributes['description'] : null,
            status: is_string($attributes['status'] ?? null) ? $attributes['status'] : null,
            creatorUserId: is_string($attributes['creator_user_id'] ?? null) ? $attributes['creator_user_id'] : null,
            workspaceId: is_string($attributes['workspace_id'] ?? null) ? $attributes['workspace_id'] : null,
            designatedVersionId: is_string($attributes['designated_version_id'] ?? null) ? $attributes['designated_version_id'] : null,
            createdAt: is_string($attributes['created_at'] ?? null) ? $attributes['created_at'] : null,
            updatedAt: is_string($attributes['updated_at'] ?? null) ? $attributes['updated_at'] : null,
            statusUpdatedAt: is_string($attributes['status_updated_at'] ?? null) ? $attributes['status_updated_at'] : null,
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
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'status' => $this->status,
            'creator_user_id' => $this->creatorUserId,
            'workspace_id' => $this->workspaceId,
            'designated_version_id' => $this->designatedVersionId,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'status_updated_at' => $this->statusUpdatedAt,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return [...$data, ...$this->extras];
    }
}
