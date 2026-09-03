<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class ImagesGenerateFixture
{
    /**
     * Mirrors the `ImagesResponse` shape from openapi-openrouter.yaml
     * for the `POST /images` endpoint.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        'created' => 1747848842,
        'data' => [
            [
                'b64_json' => 'iVBORw0KGgoAAAANSUhEUg==',
                'media_type' => 'image/png',
            ],
        ],
        'usage' => [
            'prompt_tokens' => 12,
            'completion_tokens' => 0,
            'total_tokens' => 12,
            'cost' => 0.004,
            'is_byok' => false,
        ],
    ];
}
