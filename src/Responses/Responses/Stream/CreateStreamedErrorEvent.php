<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses\Stream;

use OpenRouter\Responses\Responses\CreateStreamedResponse;

/**
 * `error` — emitted as an inline error frame within a stream, before the
 * stream is torn down. Note: the transporter's SSE parser converts error
 * frames that contain a nested `error` object into thrown `ErrorException`
 * instances; this class is used when the error arrives as a top-level event
 * with `type: error` instead.
 */
final class CreateStreamedErrorEvent extends CreateStreamedResponse
{
    /**
     * @param  array<string, mixed>  $attributes
     * @param  array<string, mixed>|null  $metadata
     */
    private function __construct(
        array $attributes,
        public readonly string $message,
        public readonly ?string $code,
        public readonly ?string $param,
        public readonly int $sequenceNumber,
        public readonly ?string $errorType,
        public readonly ?array $metadata,
    ) {
        parent::__construct('error', $attributes);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload): self
    {
        return new self(
            attributes: $payload,
            message: is_string($payload['message'] ?? null) ? $payload['message'] : '',
            code: is_string($payload['code'] ?? null) ? $payload['code'] : null,
            param: is_string($payload['param'] ?? null) ? $payload['param'] : null,
            sequenceNumber: (int) ($payload['sequence_number'] ?? 0),
            errorType: isset($payload['error_type']) && is_string($payload['error_type'])
                ? $payload['error_type']
                : (isset($payload['type']) && is_string($payload['type']) && $payload['type'] !== 'error'
                    ? $payload['type']
                    : null),
            metadata: isset($payload['metadata']) && is_array($payload['metadata']) ? $payload['metadata'] : null,
        );
    }
}
