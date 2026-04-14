<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Agent;

use OpenRouter\Agent\AgentTool;
use OpenRouter\Agent\Exceptions\MaxToolRoundsReached;
use OpenRouter\Factory;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use PHPUnit\Framework\TestCase;

final class ResponsesAgentTest extends TestCase
{
    public function testRunsToolLoopWithPreviousResponseIdChaining(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson($this->toolCallTurn(id: 'resp-1', callId: 'call-1', name: 'get_weather', arguments: '{"city":"Paris"}'));
        $http->enqueueJson($this->finalTurn(id: 'resp-2', text: 'The weather in Paris is 20°C.'));

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $captured = [];
        $result = $client->responses()->agent()
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

        $this->assertCount(2, $http->requests);
        $second = json_decode((string) $http->requests[1]->getBody(), true);
        $this->assertSame('resp-1', $second['previous_response_id']);
        // With chaining, only the new function_call_output item is sent.
        $this->assertCount(1, $second['input']);
        $this->assertSame('function_call_output', $second['input'][0]['type']);
        $this->assertSame('call-1', $second['input'][0]['call_id']);
        $this->assertSame(['temp' => 20, 'unit' => 'C'], json_decode($second['input'][0]['output'], true));
    }

    public function testMaxToolRoundsThrows(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson($this->toolCallTurn('r1', 'c1', 'loop', '{}'));
        $http->enqueueJson($this->toolCallTurn('r2', 'c2', 'loop', '{}'));

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $this->expectException(MaxToolRoundsReached::class);
        $client->responses()->agent()
            ->model('m')
            ->user('go')
            ->maxToolRounds(1)
            ->tool(AgentTool::define(name: 'loop', execute: fn () => 'x'))
            ->run();
    }

    public function testToolHandlerExceptionsAreFedBack(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson($this->toolCallTurn('r1', 'c1', 'broken', '{}'));
        $http->enqueueJson($this->finalTurn('r2', 'done'));

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $result = $client->responses()->agent()
            ->model('m')
            ->user('go')
            ->tool(AgentTool::define(name: 'broken', execute: function () { throw new \RuntimeException('oops'); }))
            ->run();

        $this->assertSame('done', $result->text());
        $second = json_decode((string) $http->requests[1]->getBody(), true);
        $this->assertSame(['error' => 'oops'], json_decode($second['input'][0]['output'], true));
    }

    /**
     * @return array<string, mixed>
     */
    private function toolCallTurn(string $id, string $callId, string $name, string $arguments): array
    {
        return [
            'id' => $id,
            'object' => 'response',
            'created_at' => 1,
            'model' => 'm',
            'status' => 'completed',
            'output' => [[
                'id' => 'fc-'.$callId,
                'type' => 'function_call',
                'call_id' => $callId,
                'name' => $name,
                'arguments' => $arguments,
                'status' => 'completed',
            ]],
            'error' => null,
            'incomplete_details' => null,
            'metadata' => null,
            'parallel_tool_calls' => true,
            'tool_choice' => 'auto',
            'tools' => [],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function finalTurn(string $id, string $text): array
    {
        return [
            'id' => $id,
            'object' => 'response',
            'created_at' => 1,
            'model' => 'm',
            'status' => 'completed',
            'output' => [[
                'id' => 'msg-'.$id,
                'type' => 'message',
                'role' => 'assistant',
                'status' => 'completed',
                'content' => [['type' => 'output_text', 'text' => $text, 'annotations' => []]],
            ]],
            'output_text' => $text,
            'error' => null,
            'incomplete_details' => null,
            'metadata' => null,
            'parallel_tool_calls' => true,
            'tool_choice' => 'auto',
            'tools' => [],
        ];
    }
}
