<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Messages\Stream;

use OpenRouter\Responses\Messages\MessagesStreamEvent;

/**
 * `ping` — a keep-alive frame emitted periodically during long streams.
 * Carries no payload.
 */
final class MessagesPingEvent extends MessagesStreamEvent
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    private function __construct(array $attributes)
    {
        parent::__construct('ping', $attributes);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload): self
    {
        return new self($payload);
    }
}
