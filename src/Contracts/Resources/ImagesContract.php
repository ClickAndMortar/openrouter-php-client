<?php

declare(strict_types=1);

namespace OpenRouter\Contracts\Resources;

use OpenRouter\Responses\Images\ImagesResult;
use OpenRouter\Responses\Images\ListImageModelEndpointsResponse;
use OpenRouter\Responses\Images\ListImageModelsResponse;
use OpenRouter\Responses\Images\Stream\ImageStreamEvent;
use OpenRouter\Responses\StreamResponse;
use OpenRouter\ValueObjects\Images\CreateImageRequest;

interface ImagesContract
{
    /**
     * Generates one or more images.
     *
     * @param  CreateImageRequest|array<string, mixed>  $parameters
     */
    public function generate(CreateImageRequest|array $parameters): ImagesResult;

    /**
     * Generates an image, streaming progressive previews and any text the model
     * emits along the way. Only models whose catalogue entry reports
     * `supports_streaming` accept this.
     *
     * @param  CreateImageRequest|array<string, mixed>  $parameters
     * @return StreamResponse<ImageStreamEvent>
     */
    public function generateStreamed(CreateImageRequest|array $parameters): StreamResponse;

    public function listModels(): ListImageModelsResponse;

    public function listEndpoints(string $author, string $slug): ListImageModelEndpointsResponse;
}
