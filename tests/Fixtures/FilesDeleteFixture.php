<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class FilesDeleteFixture
{
    /**
     * Mirrors the `FileDeleteResponse` example from openapi-openrouter.yaml
     * for the `DELETE /files/{file_id}` endpoint.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        '_shape' => 'openrouter',
        'id' => 'or_file_011CNha8iCJcU1wXNR6q4V8w',
        'type' => 'file_deleted',
    ];
}
