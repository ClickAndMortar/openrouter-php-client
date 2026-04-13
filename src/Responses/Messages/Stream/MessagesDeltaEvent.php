<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Messages\Stream;

use OpenRouter\Responses\Messages\MessagesStreamEvent;
use OpenRouter\Responses\Messages\MessagesUsage;

/**
 * `message_delta` — top-level message updates emitted near the end of the
 * stream. Carries `delta` (stop_reason, stop_sequence, container) and updated
 * `usage` token totals.
 */
final class MessagesDeltaEvent extends MessagesStreamEvent
{
    /**
     * @param  array<string, mixed>  $attributes
     * @param  array<string, mixed>  $delta
     */
    private function __construct(
        array $attributes,
        public readonly array $delta,
        public readonly ?MessagesUsage $usage,
    ) {
        parent::__construct('message_delta', $attributes);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload): self
    {
        return new self(
            attributes: $payload,
            delta: is_array($payload['delta'] ?? null) ? $payload['delta'] : [],
            usage: isset($payload['usage']) && is_array($payload['usage'])
                ? MessagesUsage::from($payload['usage'])
                : null,
        );
    }
}
