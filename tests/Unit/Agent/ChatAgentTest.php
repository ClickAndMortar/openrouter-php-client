<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Agent;

use OpenRouter\Agent\AgentTool;
use OpenRouter\Agent\AgentToolContext;
use OpenRouter\Agent\AgentToolDefinition;
use OpenRouter\Agent\Exceptions\MaxToolRoundsReached;
use OpenRouter\Agent\Exceptions\UnregisteredTool;
use OpenRouter\Factory;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use PHPUnit\Framework\TestCase;

final class ChatAgentTest extends TestCase
{
    public function testRunsToolLoopToFinalAnswer(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson($this->toolCallTurn('call-1', 'get_weather', '{"city":"Paris"}'));
        $http->enqueueJson($this->finalTurn('The weather in Paris is 20°C.'));

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $captured = [];
        $result = $client->chat()->agent()
            ->model('openai/gpt-4o')
            ->user('What is the weather in Paris?')
            ->tool(AgentTool::define(
                name: 'get_weather',
                execute: function (array $args) use (&$captured) {
                    $captured = $args;
                    return ['temp' => 20, 'unit' => 'C'];
                },
            ))
            ->run();

        $this->assertSame(['city' => 'Paris'], $captured);
        $this->assertSame('The weather in Paris is 20°C.', $result->text());
        $this->assertCount(2, $result->steps());
        $this->assertFalse($result->stoppedOnMaxRounds);

        $this->assertCount(2, $http->requests);
        $second = json_decode((string) $http->requests[1]->getBody(), true);
        $this->assertSame('user', $second['messages'][0]['role']);
        $this->assertSame('assistant', $second['messages'][1]['role']);
        $this->assertSame('call-1', $second['messages'][1]['tool_calls'][0]['id']);
        $this->assertSame('tool', $second['messages'][2]['role']);
        $this->assertSame('call-1', $second['messages'][2]['tool_call_id']);
        $toolPayload = json_decode($second['messages'][2]['content'], true);
        $this->assertSame(['temp' => 20, 'unit' => 'C'], $toolPayload);
    }

    public function testToolHandlerExceptionsAreFedBackToModel(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson($this->toolCallTurn('call-err', 'broken', '{}'));
        $http->enqueueJson($this->finalTurn('Sorry, tool failed.'));

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $result = $client->chat()->agent()
            ->model('m')
            ->user('go')
            ->tool(AgentTool::define(
                name: 'broken',
                execute: function () { throw new \RuntimeException('boom'); },
            ))
            ->run();

        $this->assertSame('Sorry, tool failed.', $result->text());
        $second = json_decode((string) $http->requests[1]->getBody(), true);
        $this->assertSame(['error' => 'boom'], json_decode($second['messages'][2]['content'], true));
    }

    public function testMaxToolRoundsThrowsWhenExceeded(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson($this->toolCallTurn('c1', 'loop', '{}'));
        $http->enqueueJson($this->toolCallTurn('c2', 'loop', '{}'));

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $this->expectException(MaxToolRoundsReached::class);
        $client->chat()->agent()
            ->model('m')
            ->user('go')
            ->maxToolRounds(1)
            ->tool(AgentTool::define(name: 'loop', execute: fn () => 'x'))
            ->run();
    }

    public function testMaxToolRoundsReturnsInsteadOfThrowingWhenDisabled(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson($this->toolCallTurn('c1', 'loop', '{}'));
        $http->enqueueJson($this->toolCallTurn('c2', 'loop', '{}'));

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $result = $client->chat()->agent()
            ->model('m')
            ->user('go')
            ->maxToolRounds(1)
            ->throwOnMaxRounds(false)
            ->tool(AgentTool::define(name: 'loop', execute: fn () => 'x'))
            ->run();

        $this->assertTrue($result->stoppedOnMaxRounds);
        $this->assertNotSame([], $result->toolCalls());
    }

    public function testZeroRoundsSkipsExecution(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson($this->toolCallTurn('c1', 'loop', '{}'));

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $result = $client->chat()->agent()
            ->model('m')
            ->user('go')
            ->maxToolRounds(0)
            ->throwOnMaxRounds(false)
            ->tool(AgentTool::define(name: 'loop', execute: fn () => 'never runs'))
            ->run();

        $this->assertCount(1, $http->requests);
        $this->assertTrue($result->stoppedOnMaxRounds);
        $this->assertCount(1, $result->toolCalls());
    }

    public function testUnregisteredToolThrows(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson($this->toolCallTurn('c1', 'unknown_tool', '{}'));

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $this->expectException(UnregisteredTool::class);
        $client->chat()->agent()
            ->model('m')
            ->user('go')
            ->run();
    }

    public function testAcceptsClassBasedToolDefinition(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson($this->toolCallTurn('call-1', 'get_weather', '{"city":"Paris"}'));
        $http->enqueueJson($this->finalTurn('Paris is 21°C.'));

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $tool = new class implements AgentToolDefinition {
            /** @var array<string, mixed>|null */
            public ?array $received = null;

            public function name(): string { return 'get_weather'; }
            public function description(): ?string { return 'Fetch the weather.'; }
            public function parameters(): array
            {
                return [
                    'type' => 'object',
                    'properties' => ['city' => ['type' => 'string']],
                    'required' => ['city'],
                ];
            }
            public function strict(): ?bool { return null; }
            public function execute(array $arguments, AgentToolContext $context): mixed
            {
                $this->received = $arguments + ['_turn' => $context->turn];
                return ['temp' => 21, 'unit' => 'C'];
            }
        };

        $result = $client->chat()->agent()
            ->model('m')
            ->user('weather?')
            ->tool($tool)
            ->run();

        $this->assertSame('Paris is 21°C.', $result->text());
        $this->assertSame(['city' => 'Paris', '_turn' => 0], $tool->received);

        // Tool declaration made it into the request with the interface-supplied schema.
        $first = json_decode((string) $http->requests[0]->getBody(), true);
        $this->assertSame('get_weather', $first['tools'][0]['function']['name']);
        $this->assertSame('Fetch the weather.', $first['tools'][0]['function']['description']);
    }

    public function testRethrowToolExceptionsSurfacesFailure(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson($this->toolCallTurn('c1', 'broken', '{}'));

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('boom');
        $client->chat()->agent()
            ->model('m')
            ->user('go')
            ->rethrowToolExceptions(true)
            ->tool(AgentTool::define(name: 'broken', execute: function () { throw new \RuntimeException('boom'); }))
            ->run();
    }

    /**
     * @return array<string, mixed>
     */
    private function toolCallTurn(string $callId, string $name, string $arguments): array
    {
        return [
            'id' => 'cc-'.$callId,
            'object' => 'chat.completion',
            'created' => 1,
            'model' => 'm',
            'choices' => [[
                'index' => 0,
                'finish_reason' => 'tool_calls',
                'message' => [
                    'role' => 'assistant',
                    'content' => null,
                    'tool_calls' => [[
                        'id' => $callId,
                        'type' => 'function',
                        'function' => ['name' => $name, 'arguments' => $arguments],
                    ]],
                ],
            ]],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function finalTurn(string $text): array
    {
        return [
            'id' => 'cc-final',
            'object' => 'chat.completion',
            'created' => 1,
            'model' => 'm',
            'choices' => [[
                'index' => 0,
                'finish_reason' => 'stop',
                'message' => ['role' => 'assistant', 'content' => $text],
            ]],
        ];
    }
}
