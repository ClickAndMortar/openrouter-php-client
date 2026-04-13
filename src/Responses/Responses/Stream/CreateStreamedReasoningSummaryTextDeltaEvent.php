<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses\Stream;

use OpenRouter\Responses\Responses\CreateStreamedResponse;

/**
 * `response.reasoning_summary_text.delta` — incremental chunks of a
 * reasoning summary text block. `$delta` holds the new text fragment.
 */
final class CreateStreamedReasoningSummaryTextDeltaEvent extends CreateStreamedResponse
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    private function __construct(
        array $attributes,
        public readonly string $delta,
        public readonly string $itemId,
        public readonly int $outputIndex,
        public readonly int $summaryIndex,
        public readonly int $sequenceNumber,
    ) {
        parent::__construct('response.reasoning_summary_text.delta', $attributes);
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
            summaryIndex: (int) ($payload['summary_index'] ?? 0),
            sequenceNumber: (int) ($payload['sequence_number'] ?? 0),
        );
    }
}
