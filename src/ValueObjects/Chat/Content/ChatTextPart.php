<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat\Content;

/**
 * Text content part. Mirrors the `ChatContentText` schema.
 */
final class ChatTextPart implements ChatContentPart
{
    public function __construct(
        public readonly string $text,
        public readonly ?ChatCacheControl $cacheControl = null,
    ) {
    }

    public function type(): string
    {
        return 'text';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'type' => $this->type(),
            'text' => $this->text,
        ];

        if ($this->cacheControl !== null) {
            $data['cache_control'] = $this->cacheControl->toArray();
        }

        return $data;
    }
}
