<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses\Stream;

use OpenRouter\Responses\Responses\CreateStreamedResponse;

/**
 * `response.output_text.delta` — the incremental text chunk events emitted as
 * the model streams its answer. `$delta` holds the new text fragment.
 */
final class CreateStreamedOutputTextDeltaEvent extends CreateStreamedResponse
{
    /**
     * @param  array<string, mixed>  $attributes
     * @param  list<array<string, mixed>>  $logprobs
     */
    private function __construct(
        array $attributes,
        public readonly string $delta,
        public readonly int $contentIndex,
        public readonly string $itemId,
        public readonly int $outputIndex,
        public readonly int $sequenceNumber,
        public readonly array $logprobs,
    ) {
        parent::__construct('response.output_text.delta', $attributes);
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
            logprobs: is_array($payload['logprobs'] ?? null) ? $payload['logprobs'] : [],
        );
    }
}
