<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Messages\Stream;

use OpenRouter\Responses\Messages\MessagesStreamEvent;

/**
 * `content_block_stop` — emitted when the content block at `index` is
 * finalized.
 */
final class MessagesContentBlockStopEvent extends MessagesStreamEvent
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    private function __construct(
        array $attributes,
        public readonly int $index,
    ) {
        parent::__construct('content_block_stop', $attributes);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload): self
    {
        return new self(
            attributes: $payload,
            index: is_int($payload['index'] ?? null) ? $payload['index'] : 0,
        );
    }
}
