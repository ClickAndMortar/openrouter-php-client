<?php

declare(strict_types=1);

namespace OpenRouter\Resources;

use OpenRouter\Contracts\Resources\ImagesContract;
use OpenRouter\Responses\Images\ImagesResult;
use OpenRouter\Responses\Images\ListImageModelEndpointsResponse;
use OpenRouter\Responses\Images\ListImageModelsResponse;
use OpenRouter\Responses\Images\Stream\ImageStreamEvent;
use OpenRouter\Responses\StreamResponse;
use OpenRouter\ValueObjects\Images\CreateImageRequest;
use OpenRouter\ValueObjects\Transporter\Payload;

final class Images implements ImagesContract
{
    use Concerns\Streamable;
    use Concerns\Transportable;

    /**
     * @see https://openrouter.ai/docs/api-reference/images
     *
     * @param  CreateImageRequest|array<string, mixed>  $parameters
     */
    public function generate(CreateImageRequest|array $parameters): ImagesResult
    {
        $params = $parameters instanceof CreateImageRequest ? $parameters->toArray() : $parameters;

        $this->ensureNotStreamed($params, 'generateStreamed');

        $response = $this->transporter->requestObject(Payload::create('images', $params));

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return ImagesResult::from($data, $response->meta());
    }

    /**
     * @param  CreateImageRequest|array<string, mixed>  $parameters
     * @return StreamResponse<ImageStreamEvent>
     */
    public function generateStreamed(CreateImageRequest|array $parameters): StreamResponse
    {
        $params = $parameters instanceof CreateImageRequest ? $parameters->toArray() : $parameters;

        $params = $this->setStreamParameter($params);

        $response = $this->transporter->requestStream(Payload::create('images', $params));

        return new StreamResponse(ImageStreamEvent::class, $response);
    }

    public function listModels(): ListImageModelsResponse
    {
        $response = $this->transporter->requestObject(Payload::list('images/models'));

        /** @var array{data: array<int, array<string, mixed>>} $data */
        $data = $response->data();

        return ListImageModelsResponse::from($data, $response->meta());
    }

    public function listEndpoints(string $author, string $slug): ListImageModelEndpointsResponse
    {
        $payload = Payload::retrieve('images/models', $author, "/{$slug}/endpoints");

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return ListImageModelEndpointsResponse::from($data, $response->meta());
    }
}
