<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses\Stream\Concerns;

/**
 * Shared shape for image generation and web search stream events that only
 * carry `item_id`, `output_index`, and `sequence_number` (no content fields).
 * Extracting the constructor+factory lets each concrete subclass stay a tiny
 * marker class.
 */
trait ItemScopedEvent
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    private function __construct(
        array $attributes,
        public readonly string $itemId,
        public readonly int $outputIndex,
        public readonly int $sequenceNumber,
    ) {
        parent::__construct(static::EVENT_TYPE, $attributes);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload): static
    {
        return new static(
            attributes: $payload,
            itemId: (string) ($payload['item_id'] ?? ''),
            outputIndex: (int) ($payload['output_index'] ?? 0),
            sequenceNumber: (int) ($payload['sequence_number'] ?? 0),
        );
    }
}
