<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class RerankFixture
{
    /**
     * Mirrors the 200 example for `POST /rerank` from openapi-openrouter.yaml.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        'id' => 'gen-rerank-1234567890-abc',
        'model' => 'cohere/rerank-v3.5',
        'provider' => 'Cohere',
        'results' => [
            [
                'index' => 0,
                'relevance_score' => 0.98,
                'document' => ['text' => 'Paris is the capital of France.'],
            ],
            [
                'index' => 1,
                'relevance_score' => 0.12,
                'document' => ['text' => 'Berlin is the capital of Germany.'],
            ],
        ],
        'usage' => [
            'search_units' => 1,
            'total_tokens' => 150,
            'cost' => 0.001,
        ],
    ];
}
