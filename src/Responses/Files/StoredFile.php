<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Files;

/**
 * A file stored by OpenRouter or, when a `provider` is supplied, by that
 * provider under your own key.
 *
 * The payload shape is negotiated per request and named by `_shape`: the
 * `openrouter` and `anthropic` shapes describe the file with `mime_type` /
 * `size_bytes` / `downloadable`, while the `openai` shape uses `bytes` /
 * `purpose` / `status`. Both are modelled here as nullable fields; use
 * {@see sizeInBytes()} to read the size without branching on the shape.
 */
final class StoredFile
{
    /**
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly string $shape,
        public readonly string $id,
        public readonly ?string $filename,
        public readonly ?string $type,
        public readonly ?string $mimeType,
        public readonly ?int $sizeBytes,
        public readonly ?string $createdAt,
        public readonly ?bool $downloadable,
        public readonly ?string $object,
        public readonly ?int $bytes,
        public readonly ?string $purpose,
        public readonly ?string $status,
        public readonly array $extras = [],
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $extras = array_diff_key($attributes, array_flip([
            '_shape', 'id', 'filename', 'type', 'mime_type', 'size_bytes',
            'created_at', 'downloadable', 'object', 'bytes', 'purpose', 'status',
        ]));

        return new self(
            shape: is_string($attributes['_shape'] ?? null) ? $attributes['_shape'] : 'openrouter',
            id: is_string($attributes['id'] ?? null) ? $attributes['id'] : '',
            filename: is_string($attributes['filename'] ?? null) ? $attributes['filename'] : null,
            type: is_string($attributes['type'] ?? null) ? $attributes['type'] : null,
            mimeType: is_string($attributes['mime_type'] ?? null) ? $attributes['mime_type'] : null,
            sizeBytes: is_int($attributes['size_bytes'] ?? null) ? $attributes['size_bytes'] : null,
            // openrouter/anthropic send an ISO-8601 string, openai a unix timestamp.
            createdAt: isset($attributes['created_at']) && is_scalar($attributes['created_at'])
                ? (string) $attributes['created_at']
                : null,
            downloadable: isset($attributes['downloadable']) ? (bool) $attributes['downloadable'] : null,
            object: is_string($attributes['object'] ?? null) ? $attributes['object'] : null,
            bytes: is_int($attributes['bytes'] ?? null) ? $attributes['bytes'] : null,
            purpose: is_string($attributes['purpose'] ?? null) ? $attributes['purpose'] : null,
            status: is_string($attributes['status'] ?? null) ? $attributes['status'] : null,
            extras: $extras,
        );
    }

    /**
     * Size in bytes, whichever spelling the negotiated shape used.
     */
    public function sizeInBytes(): ?int
    {
        return $this->sizeBytes ?? $this->bytes;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['_shape' => $this->shape, 'id' => $this->id];

        foreach ([
            'filename' => $this->filename,
            'type' => $this->type,
            'mime_type' => $this->mimeType,
            'size_bytes' => $this->sizeBytes,
            'created_at' => $this->createdAt,
            'downloadable' => $this->downloadable,
            'object' => $this->object,
            'bytes' => $this->bytes,
            'purpose' => $this->purpose,
            'status' => $this->status,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return [...$data, ...$this->extras];
    }
}
