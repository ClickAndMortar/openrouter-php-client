<?php

declare(strict_types=1);

namespace OpenRouter\Resources;

use OpenRouter\Contracts\Resources\VideosContract;
use OpenRouter\Responses\BinaryResponse;
use OpenRouter\Responses\Videos\ListVideoModelsResponse;
use OpenRouter\Responses\Videos\VideoJobResponse;
use OpenRouter\ValueObjects\Transporter\Payload;
use OpenRouter\ValueObjects\Videos\CreateVideoRequest;

final class Videos implements VideosContract
{
    use Concerns\Transportable;

    /**
     * @see https://openrouter.ai/docs/api-reference/videos
     *
     * @param  CreateVideoRequest|array<string, mixed>  $parameters
     */
    public function generate(CreateVideoRequest|array $parameters): VideoJobResponse
    {
        $params = $parameters instanceof CreateVideoRequest ? $parameters->toArray() : $parameters;

        $response = $this->transporter->requestObject(Payload::create('videos', $params));

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return VideoJobResponse::from($data, $response->meta());
    }

    public function retrieve(string $jobId): VideoJobResponse
    {
        $response = $this->transporter->requestObject(Payload::retrieve('videos', $jobId));

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return VideoJobResponse::from($data, $response->meta());
    }

    public function download(string $jobId, ?int $index = null): BinaryResponse
    {
        $query = $index === null ? [] : ['index' => $index];

        return $this->transporter->requestContent(
            Payload::retrieve('videos', $jobId, '/content', $query),
        );
    }

    public function listModels(): ListVideoModelsResponse
    {
        $response = $this->transporter->requestObject(Payload::list('videos/models'));

        /** @var array{data: array<int, array<string, mixed>>} $data */
        $data = $response->data();

        return ListVideoModelsResponse::from($data, $response->meta());
    }
}
