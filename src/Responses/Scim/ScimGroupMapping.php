<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Scim;

/**
 * Binds a SCIM group to a workspace with a role, so members provisioned into
 * that group land in the workspace automatically.
 */
final class ScimGroupMapping
{
    /**
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly string $id,
        public readonly ?string $organizationId,
        public readonly ?string $scimGroupId,
        public readonly ?string $workspaceId,
        public readonly ?string $role,
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
            'scim_group_id',
            'workspace_id',
            'role',
            'created_at',
            'updated_at',
        ]));

        return new self(
            id: is_string($attributes['id'] ?? null) ? $attributes['id'] : '',
            organizationId: is_string($attributes['organization_id'] ?? null) ? $attributes['organization_id'] : null,
            scimGroupId: is_string($attributes['scim_group_id'] ?? null) ? $attributes['scim_group_id'] : null,
            workspaceId: is_string($attributes['workspace_id'] ?? null) ? $attributes['workspace_id'] : null,
            role: is_string($attributes['role'] ?? null) ? $attributes['role'] : null,
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
            'scim_group_id' => $this->scimGroupId,
            'workspace_id' => $this->workspaceId,
            'role' => $this->role,
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
