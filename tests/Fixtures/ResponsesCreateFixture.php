<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class ResponsesCreateFixture
{
    /**
     * Mirrors the `OpenResponsesResult` example from openapi-openrouter.yaml (line 8590+),
     * enriched with a realistic `usage` object and the non-nullable `error` set to null.
     *
     * @var array{
     *     id: string,
     *     object: string,
     *     created_at: int,
     *     model: string,
     *     status: string,
     *     output: array<int, array<string, mixed>>,
     *     usage: array<string, mixed>,
     *     error: null,
     *     incomplete_details: null,
     *     instructions: null,
     *     max_output_tokens: null,
     *     metadata: null,
     *     parallel_tool_calls: bool,
     *     temperature: null,
     *     tool_choice: string,
     *     tools: array<int, mixed>,
     *     top_p: null,
     *     service_tier: null,
     * }
     */
    public const ATTRIBUTES = [
        'id' => 'resp-abc123',
        'object' => 'response',
        'created_at' => 1704067200,
        'model' => 'openai/gpt-4o',
        'status' => 'completed',
        'output' => [
            [
                'id' => 'msg-abc123',
                'type' => 'message',
                'role' => 'assistant',
                'status' => 'completed',
                'content' => [
                    [
                        'type' => 'output_text',
                        'text' => 'Hello! How can I help you today?',
                        'annotations' => [],
                    ],
                ],
            ],
        ],
        'usage' => [
            'input_tokens' => 10,
            'input_tokens_details' => ['cached_tokens' => 0],
            'output_tokens' => 25,
            'output_tokens_details' => ['reasoning_tokens' => 0],
            'total_tokens' => 35,
            'cost' => 0.0012,
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
