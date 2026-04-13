<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class ChatStreamFixture
{
    /**
     * Returns an SSE body suitable for {@see RecordingHttpClient::enqueueStream()}.
     * Three frames: opening role+content, content delta, final frame with
     * usage and finish_reason. Followed by `[DONE]`.
     */
    public static function sseBody(): string
    {
        return implode("\n", [
            'data: '.json_encode([
                'id' => 'chatcmpl-stream-1',
                'object' => 'chat.completion.chunk',
                'created' => 1704067200,
                'model' => 'openai/gpt-4o',
                'choices' => [[
                    'index' => 0,
                    'delta' => ['role' => 'assistant', 'content' => 'Hello'],
                    'finish_reason' => null,
                ]],
            ]),
            'data: '.json_encode([
                'id' => 'chatcmpl-stream-1',
                'object' => 'chat.completion.chunk',
                'created' => 1704067200,
                'model' => 'openai/gpt-4o',
                'choices' => [[
                    'index' => 0,
                    'delta' => ['content' => ' world'],
                    'finish_reason' => null,
                ]],
            ]),
            'data: '.json_encode([
                'id' => 'chatcmpl-stream-1',
                'object' => 'chat.completion.chunk',
                'created' => 1704067200,
                'model' => 'openai/gpt-4o',
                'choices' => [[
                    'index' => 0,
                    'delta' => [],
                    'finish_reason' => 'stop',
                ]],
                'usage' => [
                    'prompt_tokens' => 10,
                    'completion_tokens' => 2,
                    'total_tokens' => 12,
                ],
            ]),
            'data: [DONE]',
            '',
        ]);
    }
}
