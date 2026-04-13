<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Messages\Stream;

use OpenRouter\Responses\Messages\Deltas\DeltaFactory;
use OpenRouter\Responses\Messages\Deltas\MessagesDelta;
use OpenRouter\Responses\Messages\MessagesStreamEvent;

/**
 * `content_block_delta` — a partial update to an existing content block at
 * `index`. `delta` is a typed {@see MessagesDelta} dispatched from the
 * `delta.type` discriminator (text_delta, input_json_delta, thinking_delta,
 * signature_delta, citations_delta, compaction_delta).
 */
final class MessagesContentBlockDeltaEvent extends MessagesStreamEvent
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    private function __construct(
        array $attributes,
        public readonly int $index,
        public readonly MessagesDelta $delta,
    ) {
        parent::__construct('content_block_delta', $attributes);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload): self
    {
        $rawDelta = is_array($payload['delta'] ?? null) ? $payload['delta'] : [];

        return new self(
            attributes: $payload,
            index: is_int($payload['index'] ?? null) ? $payload['index'] : 0,
            delta: DeltaFactory::from($rawDelta),
        );
    }
}
