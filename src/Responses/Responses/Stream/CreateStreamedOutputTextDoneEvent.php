<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses\Stream;

use OpenRouter\Responses\Responses\CreateStreamedResponse;

/**
 * `response.output_text.done` — emitted when a text content part finishes
 * streaming. `$text` holds the full concatenated text.
 */
final class CreateStreamedOutputTextDoneEvent extends CreateStreamedResponse
{
    /**
     * @param  array<string, mixed>  $attributes
     * @param  list<array<string, mixed>>  $logprobs
     */
    private function __construct(
        array $attributes,
        public readonly string $text,
        public readonly int $contentIndex,
        public readonly string $itemId,
        public readonly int $outputIndex,
        public readonly int $sequenceNumber,
        public readonly array $logprobs,
    ) {
        parent::__construct('response.output_text.done', $attributes);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload): self
    {
        return new self(
            attributes: $payload,
            text: (string) ($payload['text'] ?? ''),
            contentIndex: (int) ($payload['content_index'] ?? 0),
            itemId: (string) ($payload['item_id'] ?? ''),
            outputIndex: (int) ($payload['output_index'] ?? 0),
            sequenceNumber: (int) ($payload['sequence_number'] ?? 0),
            logprobs: is_array($payload['logprobs'] ?? null) ? $payload['logprobs'] : [],
        );
    }
}
