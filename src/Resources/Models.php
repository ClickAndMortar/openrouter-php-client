<?php

declare(strict_types=1);

namespace OpenRouter\Resources;

use OpenRouter\Contracts\Resources\ModelsContract;
use OpenRouter\Responses\Models\CountResponse;
use OpenRouter\Responses\Models\ListEndpointsResponse;
use OpenRouter\Responses\Models\ListResponse;
use OpenRouter\ValueObjects\Transporter\Payload;

final class Models implements ModelsContract
{
    use Concerns\Transportable;

    /**
     * Lists all available models and their properties.
     *
     * @see https://openrouter.ai/docs/api-reference/models#list-available-models
     */
    public function list(?string $category = null, ?string $supportedParameters = null, ?string $outputModalities = null): ListResponse
    {
        $query = array_filter(
            [
                'category' => $category,
                'supported_parameters' => $supportedParameters,
                'output_modalities' => $outputModalities,
            ],
            static fn (?string $value): bool => $value !== null,
        );

        $payload = Payload::list('models', $query);

        $response = $this->transporter->requestObject($payload);

        /** @var array{data: array<int, array<string, mixed>>} $data */
        $data = $response->data();

        /** @phpstan-ignore-next-line — ListResponse::from validates the shape at runtime */
        return ListResponse::from($data, $response->meta());
    }

    /**
     * Lists models filtered by user provider preferences, privacy settings, and guardrails.
     *
     * @see https://openrouter.ai/docs/api-reference/models#list-models-filtered-by-user-provider-preferences
     */
    public function listForUser(): ListResponse
    {
        $payload = Payload::list('models/user');

        $response = $this->transporter->requestObject($payload);

        /** @var array{data: array<int, array<string, mixed>>} $data */
        $data = $response->data();

        /** @phpstan-ignore-next-line — ListResponse::from validates the shape at runtime */
        return ListResponse::from($data, $response->meta());
    }

    /**
     * Returns the total count of available models.
     */
    public function count(?string $outputModalities = null): CountResponse
    {
        $query = array_filter(
            ['output_modalities' => $outputModalities],
            static fn (?string $value): bool => $value !== null,
        );

        $payload = Payload::list('models/count', $query);

        $response = $this->transporter->requestObject($payload);

        /** @var array{data: array{count: int}} $data */
        $data = $response->data();

        return CountResponse::from($data, $response->meta());
    }

    /**
     * Lists all endpoints for a given model.
     */
    public function listEndpoints(string $author, string $slug): ListEndpointsResponse
    {
        $payload = Payload::retrieve('models', $author, "/{$slug}/endpoints");

        $response = $this->transporter->requestObject($payload);

        /** @var array{data: array<string, mixed>} $data */
        $data = $response->data();

        /** @phpstan-ignore-next-line — ListEndpointsResponse::from validates the shape at runtime */
        return ListEndpointsResponse::from($data, $response->meta());
    }
}
