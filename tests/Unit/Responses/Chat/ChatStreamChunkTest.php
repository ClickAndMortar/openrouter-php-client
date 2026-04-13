<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Responses\Chat;

use OpenRouter\Responses\Chat\Stream\ChatStreamChunk;
use PHPUnit\Framework\TestCase;

final class ChatStreamChunkTest extends TestCase
{
    public function testFromHydratesContentDelta(): void
    {
        $chunk = ChatStreamChunk::from([
            'id' => 'cc-1',
            'object' => 'chat.completion.chunk',
            'created' => 1,
            'model' => 'openai/gpt-4o',
            'choices' => [[
                'index' => 0,
                'delta' => ['role' => 'assistant', 'content' => 'Hello'],
                'finish_reason' => null,
            ]],
        ]);

        $this->assertSame('cc-1', $chunk->id);
        $this->assertCount(1, $chunk->choices);
        $this->assertSame('assistant', $chunk->choices[0]->delta->role);
        $this->assertSame('Hello', $chunk->choices[0]->delta->content);
        $this->assertNull($chunk->choices[0]->finishReason);
        $this->assertNull($chunk->usage);
    }

    public function testFromHydratesUsageOnFinalFrame(): void
    {
        $chunk = ChatStreamChunk::from([
            'id' => 'cc-1',
            'object' => 'chat.completion.chunk',
            'created' => 1,
            'model' => 'openai/gpt-4o',
            'choices' => [[
                'index' => 0,
                'delta' => [],
                'finish_reason' => 'stop',
            ]],
            'usage' => [
                'prompt_tokens' => 5,
                'completion_tokens' => 3,
                'total_tokens' => 8,
            ],
        ]);

        $this->assertNotNull($chunk->usage);
        $this->assertSame(8, $chunk->usage->totalTokens);
        $this->assertSame('stop', $chunk->choices[0]->finishReason);
    }

    public function testFromHydratesToolCallDelta(): void
    {
        $chunk = ChatStreamChunk::from([
            'id' => 'cc-1',
            'object' => 'chat.completion.chunk',
            'created' => 1,
            'model' => 'openai/gpt-4o',
            'choices' => [[
                'index' => 0,
                'delta' => [
                    'tool_calls' => [[
                        'index' => 0,
                        'id' => 'call_1',
                        'type' => 'function',
                        'function' => ['name' => 'get_weather', 'arguments' => '{"loc":'],
                    ]],
                ],
                'finish_reason' => null,
            ]],
        ]);

        $this->assertNotNull($chunk->choices[0]->delta->toolCalls);
        $this->assertCount(1, $chunk->choices[0]->delta->toolCalls);
        $tc = $chunk->choices[0]->delta->toolCalls[0];
        $this->assertSame('call_1', $tc->id);
        $this->assertSame('get_weather', $tc->functionName);
        $this->assertSame('{"loc":', $tc->functionArguments);
    }

    public function testToArrayPreservesShape(): void
    {
        $payload = [
            'id' => 'cc-1',
            'object' => 'chat.completion.chunk',
            'created' => 42,
            'model' => 'openai/gpt-4o',
            'choices' => [[
                'index' => 0,
                'delta' => ['content' => 'x'],
                'finish_reason' => null,
            ]],
        ];

        $chunk = ChatStreamChunk::from($payload);
        $array = $chunk->toArray();

        $this->assertSame('cc-1', $array['id']);
        $this->assertSame(42, $array['created']);
        $this->assertSame('x', $array['choices'][0]['delta']['content']);
    }
}
