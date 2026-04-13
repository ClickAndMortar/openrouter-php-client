<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class GenerationRetrieveFixture
{
    /**
     * Mirrors the `GenerationResponse` example from openapi-openrouter.yaml
     * for the `/generation` endpoint.
     *
     * @var array{data: array<string, mixed>}
     */
    public const ATTRIBUTES = [
        'data' => [
            'id' => 'gen-3bhGkxlo4XFrqiabUM7NDtwDzWwG',
            'upstream_id' => 'chatcmpl-791bcf62-080e-4568-87d0-94c72e3b4946',
            'total_cost' => 0.0015,
            'cache_discount' => 0.0002,
            'upstream_inference_cost' => 0.0012,
            'created_at' => '2024-07-15T23:33:19.433273+00:00',
            'model' => 'sao10k/l3-stheno-8b',
            'app_id' => 12345,
            'streamed' => true,
            'cancelled' => false,
            'provider_name' => 'Infermatic',
            'latency' => 1250,
            'moderation_latency' => 50,
            'generation_time' => 1200,
            'finish_reason' => 'stop',
            'tokens_prompt' => 10,
            'tokens_completion' => 25,
            'native_tokens_prompt' => 10,
            'native_tokens_completion' => 25,
            'native_tokens_completion_images' => 0,
            'native_tokens_reasoning' => 5,
            'native_tokens_cached' => 3,
            'num_media_prompt' => 1,
            'num_input_audio_prompt' => 0,
            'num_media_completion' => 0,
            'num_search_results' => 5,
            'origin' => 'https://openrouter.ai/',
            'usage' => 0.0015,
            'is_byok' => false,
            'native_finish_reason' => 'stop',
            'external_user' => 'user-123',
            'api_type' => null,
            'router' => null,
            'provider_responses' => null,
            'user_agent' => null,
            'http_referer' => null,
            'request_id' => 'req-1727282430-aBcDeFgHiJkLmNoPqRsT',
        ],
    ];
}
