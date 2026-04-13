<?php

declare(strict_types=1);

namespace OpenRouter\Contracts\Resources;

use OpenRouter\Responses\Embeddings\CreateEmbeddingsResponse;
use OpenRouter\Responses\Models\ListResponse;
use OpenRouter\ValueObjects\Embeddings\CreateEmbeddingsRequest;

interface EmbeddingsContract
{
    /**
     * Submits an embedding request to OpenRouter's `/embeddings` endpoint.
     * Accepts either a typed {@see CreateEmbeddingsRequest} value object or a
     * bare associative array.
     *
     * @see https://openrouter.ai/docs/api-reference/embeddings
     *
     * @param  CreateEmbeddingsRequest|array<string, mixed>  $parameters
     */
    public function generate(CreateEmbeddingsRequest|array $parameters): CreateEmbeddingsResponse;

    /**
     * Lists all available embeddings models and their properties via
     * `GET /embeddings/models`. Returns the same `ModelsListResponse` shape as
     * `/models/user`.
     *
     * @see https://openrouter.ai/docs/api-reference/embeddings
     */
    public function listModels(): ListResponse;
}
