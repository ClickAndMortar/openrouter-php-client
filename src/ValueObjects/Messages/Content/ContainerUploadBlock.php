<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Content;

/**
 * `type: container_upload` — emitted when the model uploads a file to the
 * sandbox container it has available for tool use.
 */
final class ContainerUploadBlock implements MessagesContentBlock
{
    public function __construct(
        public readonly string $fileId,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            fileId: is_string($attributes['file_id'] ?? null) ? $attributes['file_id'] : '',
        );
    }

    public function type(): string
    {
        return 'container_upload';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type(),
            'file_id' => $this->fileId,
        ];
    }
}
