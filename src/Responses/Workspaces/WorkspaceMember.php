<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Workspaces;

/**
 * A user's membership of a workspace.
 */
final class WorkspaceMember
{
    /**
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly string $id,
        public readonly string $workspaceId,
        public readonly string $userId,
        public readonly ?string $role,
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
            'workspace_id',
            'user_id',
            'role',
            'created_at',
        ]));

        return new self(
            id: is_string($attributes['id'] ?? null) ? $attributes['id'] : '',
            workspaceId: is_string($attributes['workspace_id'] ?? null) ? $attributes['workspace_id'] : '',
            userId: is_string($attributes['user_id'] ?? null) ? $attributes['user_id'] : '',
            role: is_string($attributes['role'] ?? null) ? $attributes['role'] : null,
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
            'workspace_id' => $this->workspaceId,
            'user_id' => $this->userId,
            'role' => $this->role,
            'created_at' => $this->createdAt,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return [...$data, ...$this->extras];
    }
}
