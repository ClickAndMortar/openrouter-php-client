<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses\Stream;

use OpenRouter\Responses\Responses\CreateStreamedResponse;

/**
 * `response.reasoning_text.delta` — incremental chunks of the model's
 * reasoning output (not the final answer). `$delta` holds the new text fragment.
 */
final class CreateStreamedReasoningTextDeltaEvent extends CreateStreamedResponse
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    private function __construct(
        array $attributes,
        public readonly string $delta,
        public readonly string $itemId,
        public readonly int $outputIndex,
        public readonly int $sequenceNumber,
    ) {
        parent::__construct('response.reasoning_text.delta', $attributes);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload): self
    {
        return new self(
            attributes: $payload,
            delta: (string) ($payload['delta'] ?? ''),
            itemId: (string) ($payload['item_id'] ?? ''),
            outputIndex: (int) ($payload['output_index'] ?? 0),
            sequenceNumber: (int) ($payload['sequence_number'] ?? 0),
        );
    }
}
