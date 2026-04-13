<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class ModelsListEndpointsFixture
{
    /**
     * Mirrors the `ListEndpointsResponse` example from openapi-openrouter.yaml
     * for the `/models/{author}/{slug}/endpoints` endpoint.
     *
     * @var array{data: array<string, mixed>}
     */
    public const ATTRIBUTES = [
        'data' => [
            'id' => 'openai/gpt-4',
            'name' => 'GPT-4',
            'created' => 1692901234,
            'description' => 'GPT-4 is a large multimodal model that can solve difficult problems with greater accuracy.',
            'architecture' => [
                'input_modalities' => ['text'],
                'output_modalities' => ['text'],
                'instruct_type' => 'chatml',
                'modality' => 'text->text',
                'tokenizer' => 'GPT',
            ],
            'endpoints' => [
                [
                    'name' => 'OpenAI: GPT-4',
                    'model_id' => 'openai/gpt-4',
                    'model_name' => 'GPT-4',
                    'context_length' => 8192,
                    'max_completion_tokens' => 4096,
                    'max_prompt_tokens' => 8192,
                    'pricing' => [
                        'prompt' => '0.00003',
                        'completion' => '0.00006',
                        'image' => '0',
                        'request' => '0',
                    ],
                    'provider_name' => 'OpenAI',
                    'quantization' => 'fp16',
                    'status' => 'default',
                    'supported_parameters' => ['temperature', 'top_p', 'max_tokens'],
                    'supports_implicit_caching' => true,
                    'tag' => 'openai',
                    'latency_last_30m' => [
                        'p50' => 0.25,
                        'p75' => 0.35,
                        'p90' => 0.48,
                        'p99' => 0.85,
                    ],
                    'throughput_last_30m' => [
                        'p50' => 45.2,
                        'p75' => 38.5,
                        'p90' => 28.3,
                        'p99' => 15.1,
                    ],
                    'uptime_last_1d' => 99.8,
                    'uptime_last_30m' => 99.5,
                    'uptime_last_5m' => 100,
                ],
            ],
        ],
    ];
}
