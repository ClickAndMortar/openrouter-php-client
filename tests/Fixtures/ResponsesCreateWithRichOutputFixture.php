<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

/**
 * A `CreateResponse` payload that exercises every typed output item class in
 * V1.1: message, reasoning, function_call, web_search_call, file_search_call,
 * image_generation_call, openrouter:datetime, openrouter:web_search, plus one
 * unknown `type` to exercise the {@see \OpenRouter\Responses\Responses\CreateResponseOutputUnknown}
 * fallback.
 */
final class ResponsesCreateWithRichOutputFixture
{
    /**
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        'id' => 'resp-rich-1',
        'object' => 'response',
        'created_at' => 1704067201,
        'model' => 'openai/gpt-4o',
        'status' => 'completed',
        'output' => [
            [
                'id' => 'msg-1',
                'type' => 'message',
                'role' => 'assistant',
                'status' => 'completed',
                'content' => [
                    ['type' => 'output_text', 'text' => 'Hello!', 'annotations' => []],
                ],
            ],
            [
                'id' => 'reasoning-1',
                'type' => 'reasoning',
                'status' => 'completed',
                'summary' => [
                    ['type' => 'summary_text', 'text' => 'Thought for 2s'],
                ],
                'signature' => 'sig-abc',
            ],
            [
                'id' => 'fc-1',
                'type' => 'function_call',
                'call_id' => 'call-xyz',
                'name' => 'get_weather',
                'arguments' => '{"city":"Paris"}',
                'status' => 'completed',
            ],
            [
                'id' => 'ws-1',
                'type' => 'web_search_call',
                'status' => 'completed',
                'action' => [
                    'type' => 'search',
                    'query' => 'php 8.4 news',
                    'queries' => ['php 8.4 news'],
                    'sources' => [],
                ],
            ],
            [
                'id' => 'fs-1',
                'type' => 'file_search_call',
                'queries' => ['invoice 2024'],
                'status' => 'completed',
            ],
            [
                'id' => 'img-1',
                'type' => 'image_generation_call',
                'status' => 'completed',
                'result' => 'AAAA',
            ],
            [
                'id' => 'dt-1',
                'type' => 'openrouter:datetime',
                'status' => 'completed',
                'datetime' => '2026-04-10T12:34:56Z',
                'timezone' => 'Europe/Paris',
            ],
            [
                'id' => 'orws-1',
                'type' => 'openrouter:web_search',
                'status' => 'completed',
            ],
            [
                'id' => 'future-1',
                'type' => 'some_future_type_not_in_v1',
                'future_field' => 'payload',
            ],
        ],
        'usage' => [
            'input_tokens' => 12,
            'input_tokens_details' => ['cached_tokens' => 0],
            'output_tokens' => 34,
            'output_tokens_details' => ['reasoning_tokens' => 4],
            'total_tokens' => 46,
        ],
        'error' => null,
        'incomplete_details' => null,
        'instructions' => null,
        'max_output_tokens' => null,
        'metadata' => null,
        'parallel_tool_calls' => true,
        'temperature' => null,
        'tool_choice' => 'auto',
        'tools' => [],
        'top_p' => null,
        'service_tier' => null,
    ];
}
