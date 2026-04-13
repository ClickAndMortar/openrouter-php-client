<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat\Content;

/**
 * File content part for document processing. Mirrors the `ChatContentFile`
 * schema. Either `fileData` or `fileId` may be provided; both are optional.
 */
final class ChatFilePart implements ChatContentPart
{
    public function __construct(
        public readonly ?string $fileData = null,
        public readonly ?string $fileId = null,
        public readonly ?string $filename = null,
    ) {
    }

    public function type(): string
    {
        return 'file';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $file = [];
        if ($this->fileData !== null) {
            $file['file_data'] = $this->fileData;
        }
        if ($this->fileId !== null) {
            $file['file_id'] = $this->fileId;
        }
        if ($this->filename !== null) {
            $file['filename'] = $this->filename;
        }

        return [
            'type' => $this->type(),
            'file' => $file,
        ];
    }
}
