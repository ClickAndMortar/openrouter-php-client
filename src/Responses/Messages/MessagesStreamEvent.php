<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Messages;

use OpenRouter\Responses\Messages\Stream\MessagesContentBlockDeltaEvent;
use OpenRouter\Responses\Messages\Stream\MessagesContentBlockStartEvent;
use OpenRouter\Responses\Messages\Stream\MessagesContentBlockStopEvent;
use OpenRouter\Responses\Messages\Stream\MessagesDeltaEvent;
use OpenRouter\Responses\Messages\Stream\MessagesErrorEvent;
use OpenRouter\Responses\Messages\Stream\MessagesPingEvent;
use OpenRouter\Responses\Messages\Stream\MessagesStartEvent;
use OpenRouter\Responses\Messages\Stream\MessagesStopEvent;

/**
 * Base class for a single Server-Sent Event frame from OpenRouter's streaming
 * `/messages` endpoint. `from()` dispatches to the concrete subclass matching
 * the event's `type` discriminator; unknown event types fall through to a
 * base-class instance so that callers keep working when Anthropic adds new
 * event types.
 *
 * The `type` and `attributes` properties are populated for every instance,
 * including concrete subclasses, so callers that prefer raw access via
 * `$event->attributes[...]` keep working unchanged.
 *
 * @phpstan-type MessagesStreamEventType array<string, mixed>
 */
class MessagesStreamEvent
{
    /**
     * @param  MessagesStreamEventType  $attributes
     */
    public function __construct(
        public readonly ?string $type,
        public readonly array $attributes,
    ) {
    }

    /**
     * @param  MessagesStreamEventType  $payload
     */
    public static function from(array $payload): self
    {
        $type = isset($payload['type']) && is_string($payload['type']) ? $payload['type'] : null;

        return match ($type) {
            'message_start' => MessagesStartEvent::fromPayload($payload),
            'content_block_start' => MessagesContentBlockStartEvent::fromPayload($payload),
            'content_block_delta' => MessagesContentBlockDeltaEvent::fromPayload($payload),
            'content_block_stop' => MessagesContentBlockStopEvent::fromPayload($payload),
            'message_delta' => MessagesDeltaEvent::fromPayload($payload),
            'message_stop' => MessagesStopEvent::fromPayload($payload),
            'ping' => MessagesPingEvent::fromPayload($payload),
            'error' => MessagesErrorEvent::fromPayload($payload),
            default => new self($type, $payload),
        };
    }

    /**
     * @return MessagesStreamEventType
     */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
