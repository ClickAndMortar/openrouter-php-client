<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class MessagesStreamFixture
{
    /**
     * Returns an SSE body suitable for {@see RecordingHttpClient::enqueueStream()}
     * covering every documented event type for a streamed `/messages` call:
     * message_start, content_block_start, content_block_delta (×2), ping,
     * content_block_stop, message_delta, message_stop, followed by `[DONE]`.
     */
    public static function sseBody(): string
    {
        $frames = [
            [
                'event' => 'message_start',
                'data' => [
                    'type' => 'message_start',
                    'message' => [
                        'id' => 'msg_stream_1',
                        'type' => 'message',
                        'role' => 'assistant',
                        'model' => 'anthropic/claude-sonnet-4',
                        'content' => [],
                        'stop_reason' => null,
                        'stop_sequence' => null,
                        'usage' => ['input_tokens' => 12, 'output_tokens' => 1],
                    ],
                ],
            ],
            [
                'event' => 'content_block_start',
                'data' => [
                    'type' => 'content_block_start',
                    'index' => 0,
                    'content_block' => ['type' => 'text', 'text' => ''],
                ],
            ],
            [
                'event' => 'content_block_delta',
                'data' => [
                    'type' => 'content_block_delta',
                    'index' => 0,
                    'delta' => ['type' => 'text_delta', 'text' => 'Hello'],
                ],
            ],
            [
                'event' => 'content_block_delta',
                'data' => [
                    'type' => 'content_block_delta',
                    'index' => 0,
                    'delta' => ['type' => 'text_delta', 'text' => ' world'],
                ],
            ],
            [
                'event' => 'ping',
                'data' => ['type' => 'ping'],
            ],
            [
                'event' => 'content_block_stop',
                'data' => ['type' => 'content_block_stop', 'index' => 0],
            ],
            [
                'event' => 'message_delta',
                'data' => [
                    'type' => 'message_delta',
                    'delta' => ['stop_reason' => 'end_turn', 'stop_sequence' => null],
                    'usage' => ['input_tokens' => 12, 'output_tokens' => 2],
                ],
            ],
            [
                'event' => 'message_stop',
                'data' => ['type' => 'message_stop'],
            ],
        ];

        $lines = [];
        foreach ($frames as $frame) {
            $lines[] = 'event: '.$frame['event'];
            $lines[] = 'data: '.json_encode($frame['data']);
        }
        $lines[] = 'data: [DONE]';
        $lines[] = '';

        return implode("\n", $lines);
    }
}
