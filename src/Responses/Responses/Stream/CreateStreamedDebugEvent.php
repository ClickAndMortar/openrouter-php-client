<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses\Stream;

use OpenRouter\Responses\Responses\CreateStreamedResponse;

/**
 * `response.debug` - diagnostic payload emitted when the request enabled debug output.
 */
final class CreateStreamedDebugEvent extends CreateStreamedResponse
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    private function __construct(
        array $attributes,
        /** @var array<string, mixed> */
        public readonly array $debug,
        public readonly int $sequenceNumber,
    ) {
        parent::__construct('response.debug', $attributes);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload): self
    {
        return new self(
            attributes: $payload,
            debug: is_array($payload['debug'] ?? null) ? $payload['debug'] : [],
            sequenceNumber: (int) ($payload['sequence_number'] ?? 0),
        );
    }
}
