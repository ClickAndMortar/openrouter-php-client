<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses\Stream;

use OpenRouter\Responses\Responses\CreateStreamedResponse;

/**
 * `response.reasoning_summary_text.done` — emitted when a reasoning summary
 * text block is fully produced. `$text` holds the complete summary text.
 */
final class CreateStreamedReasoningSummaryTextDoneEvent extends CreateStreamedResponse
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    private function __construct(
        array $attributes,
        public readonly string $text,
        public readonly string $itemId,
        public readonly int $outputIndex,
        public readonly int $summaryIndex,
        public readonly int $sequenceNumber,
    ) {
        parent::__construct('response.reasoning_summary_text.done', $attributes);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload): self
    {
        return new self(
            attributes: $payload,
            text: (string) ($payload['text'] ?? ''),
            itemId: (string) ($payload['item_id'] ?? ''),
            outputIndex: (int) ($payload['output_index'] ?? 0),
            summaryIndex: (int) ($payload['summary_index'] ?? 0),
            sequenceNumber: (int) ($payload['sequence_number'] ?? 0),
        );
    }
}
