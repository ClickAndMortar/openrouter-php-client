<?php

declare(strict_types=1);

namespace OpenRouter\Resources;

use OpenRouter\Contracts\Resources\EmbeddingsContract;
use OpenRouter\Responses\Embeddings\CreateEmbeddingsResponse;
use OpenRouter\Responses\Models\ListResponse;
use OpenRouter\ValueObjects\Embeddings\CreateEmbeddingsRequest;
use OpenRouter\ValueObjects\Transporter\Payload;

final class Embeddings implements EmbeddingsContract
{
    use Concerns\Transportable;

    /**
     * Submits an embedding request to OpenRouter's `/embeddings` endpoint.
     * Accepts either a typed {@see CreateEmbeddingsRequest} or a bare array.
     *
     * @see https://openrouter.ai/docs/api-reference/embeddings
     *
     * @param  CreateEmbeddingsRequest|array<string, mixed>  $parameters
     */
    public function generate(CreateEmbeddingsRequest|array $parameters): CreateEmbeddingsResponse
    {
        $params = $parameters instanceof CreateEmbeddingsRequest
            ? $parameters->toArray()
            : $parameters;

        $payload = Payload::create('embeddings', $params);

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return CreateEmbeddingsResponse::from($data, $response->meta());
    }

    /**
     * Lists all available embeddings models via `GET /embeddings/models`.
     * Returns the same `ModelsListResponse` shape as `/models/user`.
     *
     * @see https://openrouter.ai/docs/api-reference/embeddings
     */
    public function listModels(): ListResponse
    {
        $payload = Payload::list('embeddings/models');

        $response = $this->transporter->requestObject($payload);

        /** @var array{data: array<int, array<string, mixed>>} $data */
        $data = $response->data();

        /** @phpstan-ignore-next-line — ListResponse::from validates the shape at runtime */
        return ListResponse::from($data, $response->meta());
    }
}
