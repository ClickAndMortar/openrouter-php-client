<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class ChatStreamToolCallFixture
{
    /**
     * Returns an SSE body that exercises incremental tool-call argument
     * streaming: the assistant opens a tool call with `name`, then streams the
     * `arguments` JSON in two chunks before terminating with
     * `finish_reason: tool_calls`.
     */
    public static function sseBody(): string
    {
        return implode("\n", [
            'data: '.json_encode([
                'id' => 'chatcmpl-tc-1',
                'object' => 'chat.completion.chunk',
                'created' => 1704067200,
                'model' => 'openai/gpt-4o',
                'choices' => [[
                    'index' => 0,
                    'delta' => [
                        'role' => 'assistant',
                        'tool_calls' => [[
                            'index' => 0,
                            'id' => 'call_abc123',
                            'type' => 'function',
                            'function' => ['name' => 'get_weather', 'arguments' => ''],
                        ]],
                    ],
                    'finish_reason' => null,
                ]],
            ]),
            'data: '.json_encode([
                'id' => 'chatcmpl-tc-1',
                'object' => 'chat.completion.chunk',
                'created' => 1704067200,
                'model' => 'openai/gpt-4o',
                'choices' => [[
                    'index' => 0,
                    'delta' => [
                        'tool_calls' => [[
                            'index' => 0,
                            'function' => ['arguments' => '{"location":'],
                        ]],
                    ],
                    'finish_reason' => null,
                ]],
            ]),
            'data: '.json_encode([
                'id' => 'chatcmpl-tc-1',
                'object' => 'chat.completion.chunk',
                'created' => 1704067200,
                'model' => 'openai/gpt-4o',
                'choices' => [[
                    'index' => 0,
                    'delta' => [
                        'tool_calls' => [[
                            'index' => 0,
                            'function' => ['arguments' => '"Paris"}'],
                        ]],
                    ],
                    'finish_reason' => 'tool_calls',
                ]],
            ]),
            'data: [DONE]',
            '',
        ]);
    }
}
