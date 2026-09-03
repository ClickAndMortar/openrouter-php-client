<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class VideosJobFixture
{
    /**
     * Mirrors the `VideoGenerationResponse` shape from openapi-openrouter.yaml
     * for the `POST /videos` endpoint.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        'id' => 'vid_job_01HQ8Z3K4M5N6P7Q8R9S',
        'polling_url' => 'https://openrouter.ai/api/v1/videos/vid_job_01HQ8Z3K4M5N6P7Q8R9S',
        'status' => 'pending',
        'generation_id' => 'gen_01HQ8Z3K4M5N6P7Q8R9S',
        'unsigned_urls' => ['https://cdn.openrouter.ai/videos/out-0.mp4'],
        'usage' => [
            'cost' => 0.42,
            'is_byok' => false,
        ],
    ];
}
