<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses\Stream;

use OpenRouter\Responses\Responses\CreateStreamedResponse;

/**
 * `response.image_generation_call.partial_image` — a partially-rendered image
 * produced by providers that stream progressive renders. `$partialImageB64`
 * holds the base64-encoded partial frame.
 */
final class CreateStreamedImageGenerationPartialImageEvent extends CreateStreamedResponse
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    private function __construct(
        array $attributes,
        public readonly string $partialImageB64,
        public readonly int $partialImageIndex,
        public readonly string $itemId,
        public readonly int $outputIndex,
        public readonly int $sequenceNumber,
    ) {
        parent::__construct('response.image_generation_call.partial_image', $attributes);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload): self
    {
        return new self(
            attributes: $payload,
            partialImageB64: (string) ($payload['partial_image_b64'] ?? ''),
            partialImageIndex: (int) ($payload['partial_image_index'] ?? 0),
            itemId: (string) ($payload['item_id'] ?? ''),
            outputIndex: (int) ($payload['output_index'] ?? 0),
            sequenceNumber: (int) ($payload['sequence_number'] ?? 0),
        );
    }
}
