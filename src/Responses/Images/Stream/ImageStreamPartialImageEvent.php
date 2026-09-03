<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Images\Stream;

/**
 * `image_generation.partial_image` — a progressively refined preview. Frames
 * arrive in `partial_image_index` order.
 */
final class ImageStreamPartialImageEvent extends ImageStreamEvent
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    private function __construct(
        array $attributes,
        public readonly string $b64Json,
        public readonly int $partialImageIndex,
    ) {
        parent::__construct('image_generation.partial_image', $attributes);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload): self
    {
        return new self(
            attributes: $payload,
            b64Json: (string) ($payload['b64_json'] ?? ''),
            partialImageIndex: (int) ($payload['partial_image_index'] ?? 0),
        );
    }

    /**
     * Raw image bytes, or null when the payload is not valid base64.
     */
    public function binary(): ?string
    {
        $decoded = base64_decode($this->b64Json, true);

        return $decoded === false ? null : $decoded;
    }
}
