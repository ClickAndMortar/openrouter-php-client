<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\ValueObjects\Chat;

use OpenRouter\ValueObjects\Chat\Messages\AssistantMessage;
use OpenRouter\ValueObjects\Chat\Messages\DeveloperMessage;
use OpenRouter\ValueObjects\Chat\Messages\MessageFactory;
use OpenRouter\ValueObjects\Chat\Messages\SystemMessage;
use OpenRouter\ValueObjects\Chat\Messages\ToolMessage;
use OpenRouter\ValueObjects\Chat\Messages\UnknownMessage;
use OpenRouter\ValueObjects\Chat\Messages\UserMessage;
use PHPUnit\Framework\TestCase;

final class MessageFactoryTest extends TestCase
{
    public function testDispatchesSystemRole(): void
    {
        $msg = MessageFactory::from(['role' => 'system', 'content' => 'You are helpful.', 'name' => 'config']);

        $this->assertInstanceOf(SystemMessage::class, $msg);
        $this->assertSame('system', $msg->role());
        $this->assertSame('You are helpful.', $msg->content);
        $this->assertSame('config', $msg->name);
    }

    public function testDispatchesUserRole(): void
    {
        $msg = MessageFactory::from(['role' => 'user', 'content' => 'hi']);

        $this->assertInstanceOf(UserMessage::class, $msg);
        $this->assertSame('hi', $msg->content);
    }

    public function testDispatchesAssistantRoleWithToolCalls(): void
    {
        $msg = MessageFactory::from([
            'role' => 'assistant',
            'content' => null,
            'tool_calls' => [[
                'id' => 'call_1',
                'type' => 'function',
                'function' => ['name' => 'f', 'arguments' => '{}'],
            ]],
            'reasoning' => 'thinking',
        ]);

        $this->assertInstanceOf(AssistantMessage::class, $msg);
        $this->assertNull($msg->content);
        $this->assertNotNull($msg->toolCalls);
        $this->assertCount(1, $msg->toolCalls);
        $this->assertSame('call_1', $msg->toolCalls[0]->id);
        $this->assertSame('thinking', $msg->reasoning);
    }

    public function testDispatchesToolRole(): void
    {
        $msg = MessageFactory::from([
            'role' => 'tool',
            'content' => 'result',
            'tool_call_id' => 'call_1',
        ]);

        $this->assertInstanceOf(ToolMessage::class, $msg);
        $this->assertSame('call_1', $msg->toolCallId);
        $this->assertSame('result', $msg->content);
    }

    public function testDispatchesDeveloperRole(): void
    {
        $msg = MessageFactory::from(['role' => 'developer', 'content' => 'note']);

        $this->assertInstanceOf(DeveloperMessage::class, $msg);
    }

    public function testFallsBackToUnknownForUnrecognizedRole(): void
    {
        $msg = MessageFactory::from(['role' => 'future_role', 'content' => 'x', 'novel_field' => 1]);

        $this->assertInstanceOf(UnknownMessage::class, $msg);
        $this->assertSame('future_role', $msg->role());
        $this->assertSame(['role' => 'future_role', 'content' => 'x', 'novel_field' => 1], $msg->toArray());
    }

    public function testHydratesUserContentParts(): void
    {
        $msg = MessageFactory::from([
            'role' => 'user',
            'content' => [
                ['type' => 'text', 'text' => 'hello'],
                ['type' => 'image_url', 'image_url' => ['url' => 'https://x.test/i.png']],
            ],
        ]);

        $this->assertInstanceOf(UserMessage::class, $msg);
        $this->assertIsArray($msg->content);
        $this->assertCount(2, $msg->content);
        $this->assertSame('text', $msg->content[0]->type());
        $this->assertSame('image_url', $msg->content[1]->type());
    }
}
