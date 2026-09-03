<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Containers;

/**
 * A file produced inside a code-interpreter container. `source` distinguishes
 * files the assistant wrote from files that were mounted into the container.
 */
final class ContainerFile
{
    /**
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly string $id,
        public readonly string $object,
        public readonly string $containerId,
        public readonly int $bytes,
        public readonly int $createdAt,
        public readonly string $path,
        public readonly string $source,
        public readonly array $extras = [],
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $extras = array_diff_key($attributes, array_flip([
            'id', 'object', 'container_id', 'bytes', 'created_at', 'path', 'source',
        ]));

        return new self(
            id: is_string($attributes['id'] ?? null) ? $attributes['id'] : '',
            object: is_string($attributes['object'] ?? null) ? $attributes['object'] : 'container.file',
            containerId: is_string($attributes['container_id'] ?? null) ? $attributes['container_id'] : '',
            bytes: is_int($attributes['bytes'] ?? null) ? $attributes['bytes'] : 0,
            createdAt: is_int($attributes['created_at'] ?? null) ? $attributes['created_at'] : 0,
            path: is_string($attributes['path'] ?? null) ? $attributes['path'] : '',
            source: is_string($attributes['source'] ?? null) ? $attributes['source'] : '',
            extras: $extras,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'id' => $this->id,
            'object' => $this->object,
            'container_id' => $this->containerId,
            'bytes' => $this->bytes,
            'created_at' => $this->createdAt,
            'path' => $this->path,
            'source' => $this->source,
        ];

        return [...$data, ...$this->extras];
    }
}
