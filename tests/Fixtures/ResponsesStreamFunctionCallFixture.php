<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

/**
 * An SSE body covering the tool-use path: the model emits a function_call
 * output item whose arguments stream in as deltas and close with a `done`
 * event, followed by `response.completed`. Mirrors the shape the Symfony
 * command depends on when it runs its multi-turn tool loop.
 */
final class ResponsesStreamFunctionCallFixture
{
    public static function sseBody(): string
    {
        $frames = [
            [
                'type' => 'response.output_item.added',
                'sequence_number' => 1,
                'output_index' => 0,
                'item' => [
                    'id' => 'fc-1',
                    'type' => 'function_call',
                    'call_id' => 'call-xyz',
                    'name' => 'get_weather',
                    'arguments' => '',
                    'status' => 'in_progress',
                ],
            ],
            [
                'type' => 'response.function_call_arguments.delta',
                'sequence_number' => 2,
                'delta' => '{"city":',
                'item_id' => 'fc-1',
                'output_index' => 0,
            ],
            [
                'type' => 'response.function_call_arguments.delta',
                'sequence_number' => 3,
                'delta' => '"Paris"}',
                'item_id' => 'fc-1',
                'output_index' => 0,
            ],
            [
                'type' => 'response.function_call_arguments.done',
                'sequence_number' => 4,
                'arguments' => '{"city":"Paris"}',
                'name' => 'get_weather',
                'item_id' => 'fc-1',
                'output_index' => 0,
            ],
            [
                'type' => 'response.output_item.done',
                'sequence_number' => 5,
                'output_index' => 0,
                'item' => [
                    'id' => 'fc-1',
                    'type' => 'function_call',
                    'call_id' => 'call-xyz',
                    'name' => 'get_weather',
                    'arguments' => '{"city":"Paris"}',
                    'status' => 'completed',
                ],
            ],
            [
                'type' => 'response.completed',
                'sequence_number' => 6,
                'response' => [
                    'id' => 'resp-tool-1',
                    'object' => 'response',
                    'created_at' => 1704067200,
                    'model' => 'openai/gpt-4o',
                    'status' => 'completed',
                    'output' => [[
                        'id' => 'fc-1',
                        'type' => 'function_call',
                        'call_id' => 'call-xyz',
                        'name' => 'get_weather',
                        'arguments' => '{"city":"Paris"}',
                        'status' => 'completed',
                    ]],
                    'usage' => [
                        'input_tokens' => 12,
                        'input_tokens_details' => ['cached_tokens' => 0],
                        'output_tokens' => 18,
                        'output_tokens_details' => ['reasoning_tokens' => 0],
                        'total_tokens' => 30,
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
                ],
            ],
        ];

        $lines = [];
        foreach ($frames as $frame) {
            $lines[] = 'data: '.json_encode($frame, JSON_THROW_ON_ERROR);
        }
        $lines[] = 'data: [DONE]';
        $lines[] = '';

        return implode("\n", $lines);
    }
}
