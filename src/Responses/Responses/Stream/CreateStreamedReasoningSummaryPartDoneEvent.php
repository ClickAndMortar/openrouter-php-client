<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses\Stream;

use OpenRouter\Responses\Responses\CreateStreamedResponse;

/**
 * `response.reasoning_summary_part.done` — emitted when a reasoning summary
 * text block is fully produced.
 */
final class CreateStreamedReasoningSummaryPartDoneEvent extends CreateStreamedResponse
{
    /**
     * @param  array<string, mixed>  $attributes
     * @param  array<string, mixed>  $part
     */
    private function __construct(
        array $attributes,
        public readonly array $part,
        public readonly string $itemId,
        public readonly int $outputIndex,
        public readonly int $summaryIndex,
        public readonly int $sequenceNumber,
    ) {
        parent::__construct('response.reasoning_summary_part.done', $attributes);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload): self
    {
        return new self(
            attributes: $payload,
            part: is_array($payload['part'] ?? null) ? $payload['part'] : [],
            itemId: (string) ($payload['item_id'] ?? ''),
            outputIndex: (int) ($payload['output_index'] ?? 0),
            summaryIndex: (int) ($payload['summary_index'] ?? 0),
            sequenceNumber: (int) ($payload['sequence_number'] ?? 0),
        );
    }
}
