<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Agent;

use OpenRouter\Agent\AgentTool;
use OpenRouter\Agent\AgentToolContext;
use OpenRouter\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class AgentToolTest extends TestCase
{
    public function testDefineBuildsInstance(): void
    {
        $tool = AgentTool::define(
            name: 'get_weather',
            execute: fn (array $a) => ['temp' => 20],
            description: 'Fetch weather',
            parameters: ['type' => 'object', 'properties' => ['city' => ['type' => 'string']], 'required' => ['city']],
            strict: true,
        );

        $this->assertSame('get_weather', $tool->name);
        $this->assertSame('Fetch weather', $tool->description);
        $this->assertTrue($tool->strict);
        $this->assertArrayHasKey('properties', $tool->parameters);
    }

    public function testExecuteClosureReceivesArgumentsAndContext(): void
    {
        $received = null;
        $tool = AgentTool::define(
            name: 't',
            execute: function (array $args, AgentToolContext $ctx) use (&$received) {
                $received = ['args' => $args, 'turn' => $ctx->turn, 'id' => $ctx->toolCallId, 'name' => $ctx->toolName];
                return 'ok';
            },
        );

        $result = ($tool->execute)(['x' => 1], new AgentToolContext(2, 'call-7', 't'));

        $this->assertSame('ok', $result);
        $this->assertSame(['args' => ['x' => 1], 'turn' => 2, 'id' => 'call-7', 'name' => 't'], $received);
    }

    public function testEmptyNameRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        AgentTool::define(name: '', execute: fn () => null);
    }

    public function testOverlongNameRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        AgentTool::define(name: str_repeat('a', 65), execute: fn () => null);
    }
}
