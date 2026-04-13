<?php

declare(strict_types=1);

namespace OpenRouter\Contracts\Resources;

use OpenRouter\Responses\Responses\CreateResponse;
use OpenRouter\Responses\Responses\CreateStreamedResponse;
use OpenRouter\Responses\StreamResponse;
use OpenRouter\ValueObjects\Responses\CreateResponseRequest;

interface ResponsesContract
{
    /**
     * Creates a model response using OpenRouter's OpenResponses-compatible endpoint.
     *
     * Accepts either a typed {@see CreateResponseRequest} value object or a
     * bare associative array. The array form is retained for backwards
     * compatibility; new callers should prefer the typed builder.
     *
     * @see https://openrouter.ai/docs/api-reference/responses
     *
     * @param  CreateResponseRequest|array<string, mixed>  $parameters
     */
    public function send(CreateResponseRequest|array $parameters): CreateResponse;

    /**
     * Creates a streamed model response.
     *
     * Accepts either a typed {@see CreateResponseRequest} value object or a
     * bare associative array (the array form is retained for BC).
     *
     * @see https://openrouter.ai/docs/api-reference/responses
     *
     * @param  CreateResponseRequest|array<string, mixed>  $parameters
     * @return StreamResponse<CreateStreamedResponse>
     */
    public function sendStreamed(CreateResponseRequest|array $parameters): StreamResponse;
}
