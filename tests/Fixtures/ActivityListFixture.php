<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class ActivityListFixture
{
    /**
     * Mirrors the `ActivityResponse` example from openapi-openrouter.yaml
     * for the `/activity` endpoint.
     *
     * @var array{data: array<int, array<string, mixed>>}
     */
    public const ATTRIBUTES = [
        'data' => [
            [
                'date' => '2025-08-24',
                'model' => 'openai/gpt-4.1',
                'model_permaslug' => 'openai/gpt-4.1-2025-04-14',
                'endpoint_id' => '550e8400-e29b-41d4-a716-446655440000',
                'provider_name' => 'OpenAI',
                'usage' => 0.015,
                'byok_usage_inference' => 0.012,
                'requests' => 5,
                'prompt_tokens' => 50,
                'completion_tokens' => 125,
                'reasoning_tokens' => 25,
            ],
        ],
    ];
}
