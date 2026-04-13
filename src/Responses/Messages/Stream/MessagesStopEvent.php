<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Messages\Stream;

use OpenRouter\Responses\Messages\MessagesStreamEvent;

/**
 * `message_stop` — the terminal frame of a streamed `/messages` response.
 * Carries no payload beyond the `type` discriminator.
 */
final class MessagesStopEvent extends MessagesStreamEvent
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    private function __construct(array $attributes)
    {
        parent::__construct('message_stop', $attributes);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload): self
    {
        return new self($payload);
    }
}
