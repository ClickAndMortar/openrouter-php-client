<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Scim;

use OpenRouter\Enums\Scim\ScimSyncJobStatus;

/**
 * One run of a SCIM directory sync. Created queued and polled until it reaches
 * a terminal status; the group counts are only populated once it succeeds.
 */
final class ScimSyncJob
{
    /**
     * @param  string  $rawStatus  The status exactly as sent, including values
     *                             this SDK does not yet model.
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly string $id,
        public readonly ScimSyncJobStatus $status,
        public readonly string $rawStatus,
        public readonly ?int $syncedGroups,
        public readonly ?int $deletedGroups,
        public readonly ?string $errorMessage,
        public readonly ?string $createdAt,
        public readonly ?string $startedAt,
        public readonly ?string $finishedAt,
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
            'status',
            'synced_groups',
            'deleted_groups',
            'error_message',
            'created_at',
            'started_at',
            'finished_at',
        ]));

        $rawStatus = is_string($attributes['status'] ?? null) ? $attributes['status'] : '';

        return new self(
            id: is_string($attributes['id'] ?? null) ? $attributes['id'] : '',
            status: ScimSyncJobStatus::fromValue($rawStatus === '' ? null : $rawStatus),
            rawStatus: $rawStatus,
            syncedGroups: is_int($attributes['synced_groups'] ?? null) ? $attributes['synced_groups'] : null,
            deletedGroups: is_int($attributes['deleted_groups'] ?? null) ? $attributes['deleted_groups'] : null,
            errorMessage: is_string($attributes['error_message'] ?? null) ? $attributes['error_message'] : null,
            createdAt: is_string($attributes['created_at'] ?? null) ? $attributes['created_at'] : null,
            startedAt: is_string($attributes['started_at'] ?? null) ? $attributes['started_at'] : null,
            finishedAt: is_string($attributes['finished_at'] ?? null) ? $attributes['finished_at'] : null,
            extras: $extras,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'status' => $this->rawStatus,
            'synced_groups' => $this->syncedGroups,
            'deleted_groups' => $this->deletedGroups,
            'error_message' => $this->errorMessage,
            'created_at' => $this->createdAt,
            'started_at' => $this->startedAt,
            'finished_at' => $this->finishedAt,
            ...$this->extras,
        ];
    }
}
