<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

/**
 * A realistic text-only SSE body covering the core lifecycle + text-delta
 * stream events. Frames follow the flat `{type, ...}` shape documented in
 * `openapi-openrouter.yaml` (e.g. line 11253 for `response.output_text.delta`).
 */
final class ResponsesStreamFixture
{
    public static function sseBody(): string
    {
        $frames = [
            [
                'type' => 'response.created',
                'sequence_number' => 1,
                'response' => self::responseSnapshot('in_progress', output: []),
            ],
            [
                'type' => 'response.in_progress',
                'sequence_number' => 2,
                'response' => self::responseSnapshot('in_progress', output: []),
            ],
            [
                'type' => 'response.output_item.added',
                'sequence_number' => 3,
                'output_index' => 0,
                'item' => [
                    'id' => 'msg-1',
                    'type' => 'message',
                    'role' => 'assistant',
                    'status' => 'in_progress',
                    'content' => [],
                ],
            ],
            [
                'type' => 'response.content_part.added',
                'sequence_number' => 4,
                'content_index' => 0,
                'item_id' => 'msg-1',
                'output_index' => 0,
                'part' => ['type' => 'output_text', 'text' => '', 'annotations' => []],
            ],
            [
                'type' => 'response.output_text.delta',
                'sequence_number' => 5,
                'delta' => 'Hello',
                'content_index' => 0,
                'item_id' => 'msg-1',
                'output_index' => 0,
            ],
            [
                'type' => 'response.output_text.delta',
                'sequence_number' => 6,
                'delta' => ' ',
                'content_index' => 0,
                'item_id' => 'msg-1',
                'output_index' => 0,
            ],
            [
                'type' => 'response.output_text.delta',
                'sequence_number' => 7,
                'delta' => 'world!',
                'content_index' => 0,
                'item_id' => 'msg-1',
                'output_index' => 0,
            ],
            [
                'type' => 'response.output_text.done',
                'sequence_number' => 8,
                'text' => 'Hello world!',
                'content_index' => 0,
                'item_id' => 'msg-1',
                'output_index' => 0,
            ],
            [
                'type' => 'response.content_part.done',
                'sequence_number' => 9,
                'content_index' => 0,
                'item_id' => 'msg-1',
                'output_index' => 0,
                'part' => ['type' => 'output_text', 'text' => 'Hello world!', 'annotations' => []],
            ],
            [
                'type' => 'response.output_item.done',
                'sequence_number' => 10,
                'output_index' => 0,
                'item' => [
                    'id' => 'msg-1',
                    'type' => 'message',
                    'role' => 'assistant',
                    'status' => 'completed',
                    'content' => [['type' => 'output_text', 'text' => 'Hello world!', 'annotations' => []]],
                ],
            ],
            [
                'type' => 'response.completed',
                'sequence_number' => 11,
                'response' => self::responseSnapshot('completed', output: [
                    [
                        'id' => 'msg-1',
                        'type' => 'message',
                        'role' => 'assistant',
                        'status' => 'completed',
                        'content' => [['type' => 'output_text', 'text' => 'Hello world!', 'annotations' => []]],
                    ],
                ]),
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

    /**
     * @param  list<array<string, mixed>>  $output
     * @return array<string, mixed>
     */
    private static function responseSnapshot(string $status, array $output): array
    {
        return [
            'id' => 'resp-stream-1',
            'object' => 'response',
            'created_at' => 1704067200,
            'model' => 'openai/gpt-4o',
            'status' => $status,
            'output' => $output,
            'usage' => [
                'input_tokens' => 10,
                'input_tokens_details' => ['cached_tokens' => 0],
                'output_tokens' => $status === 'completed' ? 25 : 0,
                'output_tokens_details' => ['reasoning_tokens' => 0],
                'total_tokens' => $status === 'completed' ? 35 : 10,
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
}
