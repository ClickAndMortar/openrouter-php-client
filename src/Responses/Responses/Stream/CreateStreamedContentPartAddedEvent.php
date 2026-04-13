<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses\Stream;

use OpenRouter\Responses\Responses\CreateStreamedResponse;

/**
 * `response.content_part.added` — emitted when a new content part (text,
 * refusal, reasoning_text) is appended to the active message output. `$part`
 * is kept as a raw array in V1.1; typing it is a P1 follow-up.
 */
final class CreateStreamedContentPartAddedEvent extends CreateStreamedResponse
{
    /**
     * @param  array<string, mixed>  $attributes
     * @param  array<string, mixed>  $part
     */
    private function __construct(
        array $attributes,
        public readonly int $contentIndex,
        public readonly string $itemId,
        public readonly int $outputIndex,
        public readonly array $part,
        public readonly int $sequenceNumber,
    ) {
        parent::__construct('response.content_part.added', $attributes);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload): self
    {
        return new self(
            attributes: $payload,
            contentIndex: (int) ($payload['content_index'] ?? 0),
            itemId: (string) ($payload['item_id'] ?? ''),
            outputIndex: (int) ($payload['output_index'] ?? 0),
            part: is_array($payload['part'] ?? null) ? $payload['part'] : [],
            sequenceNumber: (int) ($payload['sequence_number'] ?? 0),
        );
    }
}
