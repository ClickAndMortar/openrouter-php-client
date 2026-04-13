<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Messages\Stream;

use OpenRouter\Responses\Messages\MessagesStreamEvent;

/**
 * `error` — emitted as an inline error frame within a stream. Carries the
 * nested Anthropic `error` object with `type` and `message`.
 *
 * Note: the shared {@see \OpenRouter\Responses\StreamResponse} converts
 * top-level `error` objects in the payload into thrown `ErrorException`
 * instances. This class is used when the error arrives as a typed event
 * frame (`type: error` at the top level) rather than a bare error payload.
 */
final class MessagesErrorEvent extends MessagesStreamEvent
{
    /**
     * @param  array<string, mixed>  $attributes
     * @param  array<string, mixed>  $error
     */
    private function __construct(
        array $attributes,
        public readonly array $error,
        public readonly ?string $errorType,
        public readonly string $message,
    ) {
        parent::__construct('error', $attributes);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload): self
    {
        $error = is_array($payload['error'] ?? null) ? $payload['error'] : [];

        return new self(
            attributes: $payload,
            error: $error,
            errorType: is_string($error['type'] ?? null) ? $error['type'] : null,
            message: is_string($error['message'] ?? null) ? $error['message'] : '',
        );
    }
}
