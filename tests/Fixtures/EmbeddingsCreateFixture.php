<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class EmbeddingsCreateFixture
{
    /**
     * Mirrors the 200 example for `POST /embeddings` from openapi-openrouter.yaml.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        'id' => 'embd-1234567890',
        'object' => 'list',
        'model' => 'openai/text-embedding-3-small',
        'data' => [
            [
                'object' => 'embedding',
                'index' => 0,
                'embedding' => [0.0023064255, -0.009327292, 0.015797347],
            ],
        ],
        'usage' => [
            'prompt_tokens' => 8,
            'total_tokens' => 8,
            'cost' => 0.0001,
        ],
    ];
}
