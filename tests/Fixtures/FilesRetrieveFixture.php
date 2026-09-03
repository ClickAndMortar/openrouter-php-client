<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class FilesRetrieveFixture
{
    /**
     * Mirrors the `FileResponse` example from openapi-openrouter.yaml
     * for the `GET /files/{file_id}` endpoint.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        '_shape' => 'openrouter',
        'id' => 'or_file_011CNha8iCJcU1wXNR6q4V8w',
        'type' => 'file',
        'filename' => 'document.pdf',
        'mime_type' => 'application/pdf',
        'size_bytes' => 1024000,
        'created_at' => '2025-01-01T00:00:00Z',
        'downloadable' => false,
    ];
}
