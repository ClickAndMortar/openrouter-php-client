<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class ImagesModelsFixture
{
    /**
     * Mirrors the `ImageModelsResponse` shape from openapi-openrouter.yaml
     * for the `GET /images/models` endpoint.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        'data' => [
            [
                'id' => 'openai/gpt-image-1',
                'name' => 'GPT Image 1',
                'description' => 'Image generation model.',
                'created' => 1747848842,
                'architecture' => [
                    'input_modalities' => ['text', 'image'],
                    'output_modalities' => ['image'],
                ],
                'supported_parameters' => ['size', 'quality'],
                'supports_streaming' => true,
                'endpoints' => '/api/v1/images/models/openai/gpt-image-1/endpoints',
            ],
        ],
    ];
}
