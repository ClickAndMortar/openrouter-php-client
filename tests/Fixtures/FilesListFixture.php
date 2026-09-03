<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class FilesListFixture
{
    /**
     * Mirrors the `FileListResponse` example from openapi-openrouter.yaml
     * for the `GET /files` endpoint.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        '_shape' => 'openrouter',
        'data' => [
            [
                '_shape' => 'openrouter',
                'id' => 'or_file_011CNha8iCJcU1wXNR6q4V8w',
                'type' => 'file',
                'filename' => 'document.pdf',
                'mime_type' => 'application/pdf',
                'size_bytes' => 1024000,
                'created_at' => '2025-01-01T00:00:00Z',
                'downloadable' => false,
            ],
        ],
        'has_more' => false,
        'first_id' => 'or_file_011CNha8iCJcU1wXNR6q4V8w',
        'last_id' => 'or_file_011CNha8iCJcU1wXNR6q4V8w',
        'cursor' => null,
    ];
}
