<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Images\Stream;

/**
 * `image_generation.text_chunk` — text the model emits alongside the image.
 * `phase` says whether it is `content`, `reasoning` or `draft`.
 */
final class ImageStreamTextChunkEvent extends ImageStreamEvent
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    private function __construct(
        array $attributes,
        public readonly string $text,
        public readonly string $phase,
    ) {
        parent::__construct('image_generation.text_chunk', $attributes);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload): self
    {
        return new self(
            attributes: $payload,
            text: (string) ($payload['text'] ?? ''),
            phase: (string) ($payload['phase'] ?? 'content'),
        );
    }
}
