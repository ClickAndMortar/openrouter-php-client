<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class VideosModelsFixture
{
    /**
     * Mirrors the `VideoModelsResponse` shape from openapi-openrouter.yaml
     * for the `GET /videos/models` endpoint.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        'data' => [
            [
                'id' => 'google/veo-3',
                'canonical_slug' => 'google/veo-3',
                'name' => 'Veo 3',
                'description' => 'Text-to-video generation model.',
                'created' => 1747848842,
                'supported_resolutions' => ['720p', '1080p'],
                'supported_aspect_ratios' => ['16:9', '9:16'],
                'supported_sizes' => ['1280x720'],
                'supported_durations' => [4, 8],
                'supported_frame_images' => ['first'],
                'upscale_factor' => null,
                'creativity' => null,
                'generate_audio' => true,
                'seed' => true,
                'allowed_passthrough_parameters' => [],
            ],
        ],
    ];
}
