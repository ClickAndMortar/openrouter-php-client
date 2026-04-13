<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Enums;

use OpenRouter\Enums\Chat\ChatFinishReason;
use OpenRouter\Enums\Chat\ChatMessageRole;
use PHPUnit\Framework\TestCase;

final class ChatEnumsTest extends TestCase
{
    public function testFinishReasonValues(): void
    {
        $this->assertSame(
            ['stop', 'length', 'tool_calls', 'content_filter', 'error'],
            ChatFinishReason::values(),
        );
    }

    public function testFinishReasonRoundTrip(): void
    {
        $this->assertSame(ChatFinishReason::ToolCalls, ChatFinishReason::from('tool_calls'));
        $this->assertSame('content_filter', ChatFinishReason::ContentFilter->value);
    }

    public function testMessageRoleValues(): void
    {
        $this->assertSame(
            ['system', 'user', 'assistant', 'tool', 'developer'],
            ChatMessageRole::values(),
        );
    }
}
