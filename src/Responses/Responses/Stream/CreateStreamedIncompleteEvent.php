<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses\Stream;

use OpenRouter\Responses\Responses\CreateResponse;
use OpenRouter\Responses\Responses\CreateStreamedResponse;
use OpenRouter\Responses\Responses\Stream\Concerns\HydratesNestedResponse;

/**
 * `response.incomplete` — emitted when the response terminates before
 * producing a full output (e.g. max_output_tokens reached, content filter).
 * Carries the partial response snapshot under `$response`.
 */
final class CreateStreamedIncompleteEvent extends CreateStreamedResponse
{
    use HydratesNestedResponse;

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function __construct(
        array $attributes,
        public readonly ?CreateResponse $response,
        public readonly int $sequenceNumber,
    ) {
        parent::__construct('response.incomplete', $attributes);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload): self
    {
        return new self(
            attributes: $payload,
            response: self::hydrateNestedResponse($payload),
            sequenceNumber: (int) ($payload['sequence_number'] ?? 0),
        );
    }
}
