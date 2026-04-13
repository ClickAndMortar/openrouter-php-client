<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Messages\Stream;

use OpenRouter\Responses\Messages\MessagesStreamEvent;

/**
 * `message_start` — the opening frame of a streamed `/messages` response.
 * Carries the initial `message` object (id, model, role, empty content, etc.).
 */
final class MessagesStartEvent extends MessagesStreamEvent
{
    /**
     * @param  array<string, mixed>  $attributes
     * @param  array<string, mixed>  $message
     */
    private function __construct(
        array $attributes,
        public readonly array $message,
    ) {
        parent::__construct('message_start', $attributes);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload): self
    {
        return new self(
            attributes: $payload,
            message: is_array($payload['message'] ?? null) ? $payload['message'] : [],
        );
    }
}
