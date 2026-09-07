<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class ScimSyncJobFixture
{
    /**
     * Mirrors the 200 example for `GET /scim/sync-jobs/{id}`.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        'data' => [
            'id' => '9f1c2d3e-4a5b-6c7d-8e9f-0a1b2c3d4e5f',
            'status' => 'succeeded',
            'synced_groups' => 12,
            'deleted_groups' => 3,
            'error_message' => null,
            'created_at' => '2026-09-07T06:00:00Z',
            'started_at' => '2026-09-07T06:00:05Z',
            'finished_at' => '2026-09-07T06:00:42Z',
        ],
    ];
}
