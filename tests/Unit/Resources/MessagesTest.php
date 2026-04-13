<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Resources;

use OpenRouter\Exceptions\ErrorException;
use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\Factory;
use OpenRouter\Responses\Messages\MessagesResult;
use OpenRouter\Responses\Messages\MessagesStreamEvent;
use OpenRouter\Responses\Messages\Stream\MessagesContentBlockDeltaEvent;
use OpenRouter\Responses\Messages\Stream\MessagesContentBlockStartEvent;
use OpenRouter\Responses\Messages\Stream\MessagesContentBlockStopEvent;
use OpenRouter\Responses\Messages\Stream\MessagesDeltaEvent;
use OpenRouter\Responses\Messages\Stream\MessagesErrorEvent;
use OpenRouter\Responses\Messages\Stream\MessagesPingEvent;
use OpenRouter\Responses\Messages\Stream\MessagesStartEvent;
use OpenRouter\Responses\Messages\Stream\MessagesStopEvent;
use OpenRouter\Responses\StreamResponse;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use OpenRouter\Tests\Fixtures\MessagesCreateFixture;
use OpenRouter\Tests\Fixtures\MessagesStreamFixture;
use OpenRouter\ValueObjects\Messages\Config\MessagesOutputConfig;
use OpenRouter\ValueObjects\Messages\Config\MessagesThinkingConfig;
use OpenRouter\ValueObjects\Messages\Config\MessagesToolChoice;
use OpenRouter\ValueObjects\Messages\ContextManagement\ClearThinkingEdit;
use OpenRouter\ValueObjects\Messages\ContextManagement\ContextManagement;
use OpenRouter\ValueObjects\Messages\Content\MessagesCacheControl;
use OpenRouter\ValueObjects\Messages\Content\TextBlock;
use OpenRouter\ValueObjects\Messages\Content\ToolResultBlock;
use OpenRouter\ValueObjects\Messages\Content\ToolUseBlock;
use OpenRouter\ValueObjects\Messages\CreateMessagesRequest;
use OpenRouter\ValueObjects\Messages\Messages\AssistantMessage;
use OpenRouter\ValueObjects\Messages\Messages\UserMessage;
use OpenRouter\ValueObjects\Messages\Tools\CustomTool;
use OpenRouter\ValueObjects\Messages\Tools\WebSearchTool;
use OpenRouter\ValueObjects\Responses\Plugins\WebSearchPlugin as ResponsesWebSearchPlugin;
use PHPUnit\Framework\TestCase;

final class MessagesTest extends TestCase
{
    public function testSendHitsMessagesEndpointAsPost(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(MessagesCreateFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $client->messages()->send([
            'model' => 'anthropic/claude-sonnet-4',
            'max_tokens' => 1024,
            'messages' => [['role' => 'user', 'content' => 'Hello, how are you?']],
        ]);

        $request = $http->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/messages', (string) $request->getUri());
        $this->assertSame('application/json', $request->getHeaderLine('Content-Type'));

        $body = json_decode((string) $request->getBody(), true);
        $this->assertSame('anthropic/claude-sonnet-4', $body['model']);
        $this->assertSame(1024, $body['max_tokens']);
        $this->assertSame('user', $body['messages'][0]['role']);
        $this->assertSame('Hello, how are you?', $body['messages'][0]['content']);
    }

    public function testSendReturnsTypedMessagesResult(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(MessagesCreateFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $response = $client->messages()->send([
            'model' => 'anthropic/claude-sonnet-4',
            'max_tokens' => 1024,
            'messages' => [['role' => 'user', 'content' => 'hi']],
        ]);

        $this->assertInstanceOf(MessagesResult::class, $response);
        $this->assertSame('msg_abc123', $response->id);
        $this->assertSame('message', $response->type);
        $this->assertSame('assistant', $response->role);
        $this->assertSame('anthropic/claude-sonnet-4', $response->model);
        $this->assertSame('end_turn', $response->stopReason);
        $this->assertNull($response->stopSequence);

        $this->assertCount(1, $response->content);
        $this->assertInstanceOf(\OpenRouter\ValueObjects\Messages\Content\TextBlock::class, $response->content[0]);
        $this->assertSame('text', $response->content[0]->type());
        $this->assertSame(
            "I'm doing well, thank you for asking! How can I help you today?",
            $response->content[0]->text,
        );

        $this->assertNotNull($response->usage);
        $this->assertSame(12, $response->usage->inputTokens);
        $this->assertSame(18, $response->usage->outputTokens);
        $this->assertSame(0, $response->usage->cacheCreationInputTokens);
        $this->assertSame(0, $response->usage->cacheReadInputTokens);
        $this->assertSame(0.0009, $response->usage->cost);
        $this->assertFalse($response->usage->isByok);
        $this->assertSame('standard', $response->usage->speed);
    }

    public function testSendThrowsWhenStreamParameterIsTrue(): void
    {
        $client = (new Factory())
            ->withApiKey('sk-or-test')
            ->withHttpClient(new RecordingHttpClient())
            ->make();

        $this->expectException(InvalidArgumentException::class);
        $client->messages()->send([
            'model' => 'anthropic/claude-sonnet-4',
            'max_tokens' => 1024,
            'messages' => [['role' => 'user', 'content' => 'hi']],
            'stream' => true,
        ]);
    }

    public function testSendAcceptsCreateMessagesRequestValueObject(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(MessagesCreateFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $request = new CreateMessagesRequest(
            messages: [new UserMessage('Hello')],
            model: 'anthropic/claude-sonnet-4',
            maxTokens: 512,
            temperature: 0.7,
            system: 'You are helpful.',
            stopSequences: ['\n\nHuman:'],
            extras: ['custom_field' => 'passthrough'],
        );

        $response = $client->messages()->send($request);

        $this->assertInstanceOf(MessagesResult::class, $response);

        $body = json_decode((string) $http->lastRequest()->getBody(), true);
        $this->assertSame('anthropic/claude-sonnet-4', $body['model']);
        $this->assertSame(512, $body['max_tokens']);
        $this->assertSame(0.7, $body['temperature']);
        $this->assertSame('You are helpful.', $body['system']);
        $this->assertSame(['\n\nHuman:'], $body['stop_sequences']);
        $this->assertSame('passthrough', $body['custom_field']);
        $this->assertSame('user', $body['messages'][0]['role']);
        $this->assertSame('Hello', $body['messages'][0]['content']);
    }

    public function testCreateMessagesRequestRejectsEmptyMessages(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CreateMessagesRequest(messages: []);
    }

    public function testSendStreamedInjectsStreamParameterAndParsesEvents(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueStream(MessagesStreamFixture::sseBody());

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        /** @var StreamResponse<MessagesStreamEvent> $stream */
        $stream = $client->messages()->sendStreamed([
            'model' => 'anthropic/claude-sonnet-4',
            'max_tokens' => 1024,
            'messages' => [['role' => 'user', 'content' => 'hi']],
        ]);

        $request = $http->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/messages', (string) $request->getUri());
        $body = json_decode((string) $request->getBody(), true);
        $this->assertTrue($body['stream']);

        $events = [];
        foreach ($stream as $event) {
            $events[] = $event;
        }

        $this->assertCount(8, $events);
        $this->assertInstanceOf(MessagesStartEvent::class, $events[0]);
        $this->assertInstanceOf(MessagesContentBlockStartEvent::class, $events[1]);
        $this->assertInstanceOf(MessagesContentBlockDeltaEvent::class, $events[2]);
        $this->assertInstanceOf(MessagesContentBlockDeltaEvent::class, $events[3]);
        $this->assertInstanceOf(MessagesPingEvent::class, $events[4]);
        $this->assertInstanceOf(MessagesContentBlockStopEvent::class, $events[5]);
        $this->assertInstanceOf(MessagesDeltaEvent::class, $events[6]);
        $this->assertInstanceOf(MessagesStopEvent::class, $events[7]);

        $this->assertSame('msg_stream_1', $events[0]->message['id']);
        $this->assertSame(0, $events[1]->index);
        $this->assertSame('text', $events[1]->contentBlock->type());

        $this->assertSame('text_delta', $events[2]->delta->type());
        $this->assertSame('Hello', $events[2]->delta->text);
        $this->assertSame(' world', $events[3]->delta->text);

        $this->assertSame(0, $events[5]->index);

        $this->assertSame('end_turn', $events[6]->delta['stop_reason']);
        $this->assertNotNull($events[6]->usage);
        $this->assertSame(2, $events[6]->usage->outputTokens);
    }

    public function testSendStreamedAcceptsTypedRequestBuilder(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueStream(MessagesStreamFixture::sseBody());

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $request = new CreateMessagesRequest(
            messages: [new UserMessage('Hi')],
            model: 'anthropic/claude-sonnet-4',
            maxTokens: 256,
        );

        $stream = $client->messages()->sendStreamed($request);

        $count = 0;
        foreach ($stream as $event) {
            $this->assertInstanceOf(MessagesStreamEvent::class, $event);
            $count++;
        }

        $body = json_decode((string) $http->lastRequest()->getBody(), true);
        $this->assertSame('anthropic/claude-sonnet-4', $body['model']);
        $this->assertTrue($body['stream'], 'Streamable trait must inject stream=true even for VO input');
        $this->assertSame(8, $count);
    }

    public function testStreamPromotesErrorFrameToException(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueStream(implode("\n", [
            'event: error',
            'data: '.json_encode([
                'type' => 'error',
                'error' => ['type' => 'overloaded_error', 'message' => 'overloaded'],
            ]),
            'data: [DONE]',
            '',
        ]));

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $stream = $client->messages()->sendStreamed([
            'model' => 'anthropic/claude-sonnet-4',
            'max_tokens' => 32,
            'messages' => [['role' => 'user', 'content' => 'hi']],
        ]);

        $this->expectException(ErrorException::class);
        $this->expectExceptionMessage('overloaded');

        foreach ($stream as $_) {
            // StreamResponse throws on the error frame before any event is yielded.
        }
    }

    public function testMessagesErrorEventHydratesDirectly(): void
    {
        // MessagesErrorEvent is reachable when future payloads carry a typed
        // error frame without the nested `error` object that StreamResponse
        // promotes to an exception. Exercise the hydrator directly.
        $event = MessagesErrorEvent::fromPayload([
            'type' => 'error',
            'error' => ['type' => 'overloaded_error', 'message' => 'overloaded'],
        ]);

        $this->assertSame('error', $event->type);
        $this->assertSame('overloaded_error', $event->errorType);
        $this->assertSame('overloaded', $event->message);
    }

    public function testStreamDispatchesUnknownTypeToBaseClass(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueStream(implode("\n", [
            'event: brand_new_event',
            'data: '.json_encode(['type' => 'brand_new_event', 'foo' => 'bar']),
            'data: [DONE]',
            '',
        ]));

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $stream = $client->messages()->sendStreamed([
            'model' => 'anthropic/claude-sonnet-4',
            'max_tokens' => 32,
            'messages' => [['role' => 'user', 'content' => 'hi']],
        ]);

        $events = iterator_to_array($stream, false);
        $this->assertCount(1, $events);
        $this->assertSame(MessagesStreamEvent::class, $events[0]::class);
        $this->assertSame('brand_new_event', $events[0]->type);
        $this->assertSame('bar', $events[0]->attributes['foo']);
    }

    public function testSendRaisesErrorExceptionOnApiError(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(
            ['error' => ['code' => 401, 'message' => 'Invalid API key']],
            401,
        );

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        try {
            $client->messages()->send([
                'model' => 'anthropic/claude-sonnet-4',
                'max_tokens' => 32,
                'messages' => [['role' => 'user', 'content' => 'hi']],
            ]);
            $this->fail('Expected ErrorException');
        } catch (ErrorException $e) {
            $this->assertSame('Invalid API key', $e->getMessage());
            $this->assertSame(401, $e->getStatusCode());
        }
    }

    public function testCreateMessagesRequestSerializesAllTypedInputs(): void
    {
        $request = new CreateMessagesRequest(
            messages: [
                new UserMessage([new TextBlock('Weather?')]),
                new AssistantMessage([
                    new ToolUseBlock(id: 'toolu_1', name: 'get_weather', input: ['location' => 'Paris']),
                ]),
                new UserMessage([
                    new ToolResultBlock(toolUseId: 'toolu_1', content: '72F and sunny'),
                ]),
            ],
            model: 'anthropic/claude-sonnet-4',
            maxTokens: 1024,
            system: [new TextBlock('You are helpful.', new MessagesCacheControl())],
            tools: [
                new CustomTool(name: 'get_weather', inputSchema: ['type' => 'object']),
                new WebSearchTool(),
            ],
            toolChoice: MessagesToolChoice::auto(disableParallelToolUse: true),
            thinking: MessagesThinkingConfig::enabled(2048),
            contextManagement: new ContextManagement([ClearThinkingEdit::keepTurns(3)]),
            cacheControl: new MessagesCacheControl(ttl: '1h'),
            outputConfig: MessagesOutputConfig::jsonSchema(['type' => 'object'], 'medium'),
            plugins: [new ResponsesWebSearchPlugin(enabled: true)],
        );

        $body = $request->toArray();

        $this->assertSame('Weather?', $body['messages'][0]['content'][0]['text']);
        $this->assertSame('tool_use', $body['messages'][1]['content'][0]['type']);
        $this->assertSame('toolu_1', $body['messages'][1]['content'][0]['id']);
        $this->assertSame('tool_result', $body['messages'][2]['content'][0]['type']);
        $this->assertSame('72F and sunny', $body['messages'][2]['content'][0]['content']);

        $this->assertSame('You are helpful.', $body['system'][0]['text']);
        $this->assertSame('ephemeral', $body['system'][0]['cache_control']['type']);

        $this->assertSame('custom', $body['tools'][0]['type']);
        $this->assertSame('get_weather', $body['tools'][0]['name']);
        $this->assertSame('web_search_20260209', $body['tools'][1]['type']);

        $this->assertSame('auto', $body['tool_choice']['type']);
        $this->assertTrue($body['tool_choice']['disable_parallel_tool_use']);

        $this->assertSame(['type' => 'enabled', 'budget_tokens' => 2048], $body['thinking']);

        $this->assertSame('clear_thinking_20251015', $body['context_management']['edits'][0]['type']);
        $this->assertSame(['turns' => 3], $body['context_management']['edits'][0]['keep']);

        $this->assertSame(['type' => 'ephemeral', 'ttl' => '1h'], $body['cache_control']);

        $this->assertSame('json_schema', $body['output_config']['format']['type']);
        $this->assertSame('medium', $body['output_config']['effort']);

        $this->assertSame('web', $body['plugins'][0]['id']);
        $this->assertTrue($body['plugins'][0]['enabled']);
    }

    public function testMessagesResultHydratesContentBlocksAndContainer(): void
    {
        $http = new RecordingHttpClient();
        $fixture = MessagesCreateFixture::ATTRIBUTES;
        $fixture['container'] = ['id' => 'ctr_1', 'expires_at' => '2026-05-01T00:00:00Z'];
        $fixture['content'][] = [
            'type' => 'tool_use',
            'id' => 'toolu_xyz',
            'name' => 'lookup',
            'input' => ['q' => 'php'],
        ];
        $http->enqueueJson($fixture);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $response = $client->messages()->send([
            'model' => 'anthropic/claude-sonnet-4',
            'max_tokens' => 1024,
            'messages' => [['role' => 'user', 'content' => 'hi']],
        ]);

        $this->assertCount(2, $response->content);
        $this->assertInstanceOf(TextBlock::class, $response->content[0]);
        $this->assertInstanceOf(ToolUseBlock::class, $response->content[1]);
        $this->assertSame('toolu_xyz', $response->content[1]->id);
        $this->assertSame(['q' => 'php'], $response->content[1]->input);

        $this->assertNotNull($response->container);
        $this->assertSame('ctr_1', $response->container->id);
        $this->assertSame('2026-05-01T00:00:00Z', $response->container->expiresAt);
    }
}
