<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class ImagesModelEndpointsFixture
{
    /**
     * Mirrors the `ImageModelEndpointsResponse` shape from openapi-openrouter.yaml
     * for the `GET /images/models/{author}/{slug}/endpoints` endpoint.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        'id' => 'openai/gpt-image-1',
        'endpoints' => [
            [
                'provider_name' => 'OpenAI',
                'provider_slug' => 'openai',
                'provider_tag' => null,
                'supported_parameters' => ['size', 'quality'],
                'allowed_passthrough_parameters' => [],
                'supports_streaming' => true,
                'pricing' => [],
            ],
        ],
    ];
}
