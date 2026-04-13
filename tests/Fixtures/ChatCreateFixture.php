<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class ChatCreateFixture
{
    /**
     * Mirrors the `ChatResult` example from openapi-openrouter.yaml (line 3874+),
     * enriched with realistic OpenRouter `usage.cost`/`cost_details`/`prompt_tokens_details`
     * fields and a tool-call assistant message in a second choice for variety.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        'id' => 'chatcmpl-abc123',
        'object' => 'chat.completion',
        'created' => 1704067200,
        'model' => 'openai/gpt-4o',
        'system_fingerprint' => 'fp_44709d6fcb',
        'service_tier' => 'default',
        'provider' => 'OpenAI',
        'choices' => [
            [
                'index' => 0,
                'finish_reason' => 'stop',
                'native_finish_reason' => 'stop',
                'message' => [
                    'role' => 'assistant',
                    'content' => 'The capital of France is Paris.',
                    'refusal' => null,
                ],
                'logprobs' => null,
            ],
            [
                'index' => 1,
                'finish_reason' => 'tool_calls',
                'message' => [
                    'role' => 'assistant',
                    'content' => null,
                    'tool_calls' => [
                        [
                            'id' => 'call_abc123',
                            'type' => 'function',
                            'function' => [
                                'name' => 'get_weather',
                                'arguments' => '{"location":"Paris"}',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'usage' => [
            'prompt_tokens' => 10,
            'completion_tokens' => 15,
            'total_tokens' => 25,
            'prompt_tokens_details' => [
                'cached_tokens' => 2,
            ],
            'completion_tokens_details' => [
                'reasoning_tokens' => 5,
            ],
            'cost' => 0.0012,
            'cost_details' => [
                'upstream_inference_input_cost' => 0.0004,
                'upstream_inference_output_cost' => 0.0008,
            ],
        ],
    ];
}
