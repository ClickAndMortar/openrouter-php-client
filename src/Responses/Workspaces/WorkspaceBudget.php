<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Workspaces;

/**
 * A spend cap on a workspace for one reset interval. At most one budget
 * exists per interval, so `setBudget()` is an upsert.
 */
final class WorkspaceBudget
{
    /**
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly string $id,
        public readonly string $workspaceId,
        public readonly ?float $limitUsd,
        public readonly ?string $resetInterval,
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
            'workspace_id',
            'limit_usd',
            'reset_interval',
            'created_at',
            'updated_at',
        ]));

        return new self(
            id: is_string($attributes['id'] ?? null) ? $attributes['id'] : '',
            workspaceId: is_string($attributes['workspace_id'] ?? null) ? $attributes['workspace_id'] : '',
            limitUsd: isset($attributes['limit_usd']) && is_numeric($attributes['limit_usd']) ? (float) $attributes['limit_usd'] : null,
            resetInterval: is_string($attributes['reset_interval'] ?? null) ? $attributes['reset_interval'] : null,
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
            'workspace_id' => $this->workspaceId,
            'limit_usd' => $this->limitUsd,
            'reset_interval' => $this->resetInterval,
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
