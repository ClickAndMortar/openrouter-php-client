<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Input\Content;

use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * File content part inside an input message. Mirrors the `InputFile` schema
 * from the OpenRouter OpenAPI spec. Exactly one of `fileId`, `fileUrl`, or
 * `fileData` must be provided — they are mutually exclusive per the spec.
 */
final class InputFilePart implements FunctionCallOutputContentPart
{
    public function __construct(
        public readonly ?string $fileId = null,
        public readonly ?string $fileUrl = null,
        public readonly ?string $fileData = null,
        public readonly ?string $filename = null,
    ) {
        $sources = array_filter([
            $this->fileId,
            $this->fileUrl,
            $this->fileData,
        ], static fn (?string $v): bool => $v !== null);

        if (count($sources) === 0) {
            throw new InvalidArgumentException(
                'InputFilePart requires one of fileId, fileUrl, or fileData',
            );
        }
    }

    public function type(): string
    {
        return 'input_file';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['type' => $this->type()];

        if ($this->fileId !== null) {
            $data['file_id'] = $this->fileId;
        }
        if ($this->fileUrl !== null) {
            $data['file_url'] = $this->fileUrl;
        }
        if ($this->fileData !== null) {
            $data['file_data'] = $this->fileData;
        }
        if ($this->filename !== null) {
            $data['filename'] = $this->filename;
        }

        return $data;
    }
}
