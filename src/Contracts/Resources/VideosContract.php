<?php

declare(strict_types=1);

namespace OpenRouter\Contracts\Resources;

use OpenRouter\Responses\BinaryResponse;
use OpenRouter\Responses\Videos\ListVideoModelsResponse;
use OpenRouter\Responses\Videos\VideoJobResponse;
use OpenRouter\ValueObjects\Videos\CreateVideoRequest;

interface VideosContract
{
    /**
     * Submits an asynchronous video generation job.
     *
     * @param  CreateVideoRequest|array<string, mixed>  $parameters
     */
    public function generate(CreateVideoRequest|array $parameters): VideoJobResponse;

    /**
     * Polls a previously submitted job.
     */
    public function retrieve(string $jobId): VideoJobResponse;

    /**
     * Downloads the rendered video. `$index` selects one output when the job
     * produced several.
     */
    public function download(string $jobId, ?int $index = null): BinaryResponse;

    public function listModels(): ListVideoModelsResponse;
}
