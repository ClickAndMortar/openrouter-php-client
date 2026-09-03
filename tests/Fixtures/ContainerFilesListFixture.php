<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class ContainerFilesListFixture
{
    /**
     * Mirrors the `ContainerFileListResponse` example from openapi-openrouter.yaml
     * for the `GET /containers/{container_id}/files` endpoint.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        'object' => 'list',
        'data' => [
            [
                'id' => 'cfile_682e0e8a43c88191a7978f477a09bdf5',
                'object' => 'container.file',
                'container_id' => 'cntr_682e30645a488191b6363d0b9b992d3a',
                'bytes' => 880,
                'created_at' => 1747848842,
                'path' => '/mnt/data/88e12fa4-6c64-4725-ab63-695e85602e73.png',
                'source' => 'assistant',
            ],
        ],
        'first_id' => 'cfile_682e0e8a43c88191a7978f477a09bdf5',
        'last_id' => 'cfile_682e0e8a43c88191a7978f477a09bdf5',
        'has_more' => false,
    ];
}
