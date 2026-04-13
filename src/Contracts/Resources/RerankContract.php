<?php

declare(strict_types=1);

namespace OpenRouter\Contracts\Resources;

use OpenRouter\Responses\Rerank\RerankResponse;
use OpenRouter\ValueObjects\Rerank\RerankRequest;

interface RerankContract
{
    /**
     * Submits a rerank request to OpenRouter's `/rerank` endpoint. Accepts
     * either a typed {@see RerankRequest} value object or a bare array.
     *
     * @see https://openrouter.ai/docs/api-reference/rerank
     *
     * @param  RerankRequest|array<string, mixed>  $parameters
     */
    public function rerank(RerankRequest|array $parameters): RerankResponse;
}
