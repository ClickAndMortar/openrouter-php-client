<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses\Stream;

use OpenRouter\Responses\Responses\CreateStreamedResponse;

/**
 * `response.refusal.delta` — emitted when the model refuses to produce the
 * requested content. `$delta` holds an incremental chunk of the refusal text.
 */
final class CreateStreamedRefusalDeltaEvent extends CreateStreamedResponse
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    private function __construct(
        array $attributes,
        public readonly string $delta,
        public readonly int $contentIndex,
        public readonly string $itemId,
        public readonly int $outputIndex,
        public readonly int $sequenceNumber,
    ) {
        parent::__construct('response.refusal.delta', $attributes);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload): self
    {
        return new self(
            attributes: $payload,
            delta: (string) ($payload['delta'] ?? ''),
            contentIndex: (int) ($payload['content_index'] ?? 0),
            itemId: (string) ($payload['item_id'] ?? ''),
            outputIndex: (int) ($payload['output_index'] ?? 0),
            sequenceNumber: (int) ($payload['sequence_number'] ?? 0),
        );
    }
}
