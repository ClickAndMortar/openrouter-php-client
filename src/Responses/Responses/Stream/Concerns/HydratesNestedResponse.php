<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses\Stream\Concerns;

use OpenRouter\Responses\Meta\MetaInformation;
use OpenRouter\Responses\Responses\CreateResponse;
use Throwable;

/**
 * Shared hydration helper for lifecycle stream events that embed a full
 * {@see CreateResponse} snapshot under the `response` key. On malformed
 * payloads the helper returns null instead of throwing, so a degraded event
 * still reaches the caller (via the base-class fields) rather than breaking
 * the entire stream.
 */
trait HydratesNestedResponse
{
    /**
     * @param  array<string, mixed>  $payload
     */
    private static function hydrateNestedResponse(array $payload): ?CreateResponse
    {
        if (! isset($payload['response']) || ! is_array($payload['response'])) {
            return null;
        }

        $response = $payload['response'];
        foreach (['id', 'object', 'created_at', 'model', 'status'] as $required) {
            if (! isset($response[$required])) {
                return null;
            }
        }

        try {
            /** @phpstan-ignore-next-line — CreateResponse::from validates shape at runtime */
            return CreateResponse::from($response, MetaInformation::from([]));
        } catch (Throwable) {
            return null;
        }
    }
}
