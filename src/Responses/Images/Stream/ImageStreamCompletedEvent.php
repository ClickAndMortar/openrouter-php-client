<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Images\Stream;

use OpenRouter\Responses\Images\ImagesUsage;

/**
 * `image_generation.completed` — the final image, base64-encoded.
 */
final class ImageStreamCompletedEvent extends ImageStreamEvent
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    private function __construct(
        array $attributes,
        public readonly string $b64Json,
        public readonly int $created,
        public readonly ?string $mediaType,
        public readonly ?ImagesUsage $usage,
    ) {
        parent::__construct('image_generation.completed', $attributes);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload): self
    {
        return new self(
            attributes: $payload,
            b64Json: (string) ($payload['b64_json'] ?? ''),
            created: (int) ($payload['created'] ?? 0),
            mediaType: is_string($payload['media_type'] ?? null) ? $payload['media_type'] : null,
            usage: isset($payload['usage']) && is_array($payload['usage'])
                ? ImagesUsage::from($payload['usage'])
                : null,
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
