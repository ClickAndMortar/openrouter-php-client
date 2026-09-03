<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Images\Stream;

/**
 * Base class for a single SSE frame from a streamed `POST /images` call.
 * `from()` dispatches to the concrete subclass matching the event's `type`
 * discriminator; unknown types fall through to a base-class instance so that
 * callers keep working when OpenRouter adds new frames.
 */
class ImageStreamEvent
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(
        public readonly ?string $type,
        public readonly array $attributes,
    ) {
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function from(array $payload): self
    {
        $type = is_string($payload['type'] ?? null) ? $payload['type'] : null;

        return match ($type) {
            'image_generation.completed' => ImageStreamCompletedEvent::fromPayload($payload),
            'image_generation.partial_image' => ImageStreamPartialImageEvent::fromPayload($payload),
            'image_generation.text_chunk' => ImageStreamTextChunkEvent::fromPayload($payload),
            default => new self($type, $payload),
        };
    }
}
