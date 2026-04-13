<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Resources;

use OpenRouter\Exceptions\ErrorException;
use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\Factory;
use OpenRouter\Responses\Chat\ChatResult;
use OpenRouter\Responses\Chat\Stream\ChatStreamChunk;
use OpenRouter\Responses\StreamResponse;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use OpenRouter\Tests\Fixtures\ChatCreateFixture;
use OpenRouter\Tests\Fixtures\ChatStreamFixture;
use OpenRouter\Tests\Fixtures\ChatStreamToolCallFixture;
use OpenRouter\ValueObjects\Chat\Content\ChatTextPart;
use OpenRouter\ValueObjects\Chat\CreateChatRequest;
use OpenRouter\ValueObjects\Chat\Messages\SystemMessage;
use OpenRouter\ValueObjects\Chat\Messages\UserMessage;
use PHPUnit\Framework\TestCase;

final class ChatTest extends TestCase
{
    public function testSendHitsChatCompletionsEndpointAsPost(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ChatCreateFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $client->chat()->send([
            'model' => 'openai/gpt-4o',
            'messages' => [['role' => 'user', 'content' => 'Hi']],
        ]);

        $request = $http->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/chat/completions', (string) $request->getUri());
        $this->assertSame('application/json', $request->getHeaderLine('Content-Type'));

        $body = json_decode((string) $request->getBody(), true);
        $this->assertSame('openai/gpt-4o', $body['model']);
        $this->assertSame('user', $body['messages'][0]['role']);
        $this->assertSame('Hi', $body['messages'][0]['content']);
    }

    public function testSendReturnsTypedChatResult(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ChatCreateFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $response = $client->chat()->send([
            'model' => 'openai/gpt-4o',
            'messages' => [['role' => 'user', 'content' => 'hi']],
        ]);

        $this->assertInstanceOf(ChatResult::class, $response);
        $this->assertSame('chatcmpl-abc123', $response->id);
        $this->assertSame('chat.completion', $response->object);
        $this->assertSame('openai/gpt-4o', $response->model);
        $this->assertSame(1704067200, $response->created);
        $this->assertSame('fp_44709d6fcb', $response->systemFingerprint);
        $this->assertSame('OpenAI', $response->provider);

        $this->assertCount(2, $response->choices);
        $first = $response->choices[0];
        $this->assertSame(0, $first->index);
        $this->assertSame('stop', $first->finishReason);
        $this->assertSame('stop', $first->nativeFinishReason);
        $this->assertSame('assistant', $first->message->role);
        $this->assertSame('The capital of France is Paris.', $first->message->content);
        $this->assertSame([], $first->message->toolCalls);

        $second = $response->choices[1];
        $this->assertSame('tool_calls', $second->finishReason);
        $this->assertNull($second->message->content);
        $this->assertCount(1, $second->message->toolCalls);
        $this->assertSame('call_abc123', $second->message->toolCalls[0]->id);
        $this->assertSame('get_weather', $second->message->toolCalls[0]->functionName);
        $this->assertSame('{"location":"Paris"}', $second->message->toolCalls[0]->functionArguments);

        $this->assertNotNull($response->usage);
        $this->assertSame(10, $response->usage->promptTokens);
        $this->assertSame(15, $response->usage->completionTokens);
        $this->assertSame(25, $response->usage->totalTokens);
        $this->assertSame(2, $response->usage->cachedTokens());
        $this->assertSame(5, $response->usage->reasoningTokens());
        $this->assertSame(0.0012, $response->usage->cost);
        $this->assertNotNull($response->usage->costDetails);
        $this->assertSame(0.0004, $response->usage->costDetails->upstreamInferenceInputCost);
    }

    public function testSendThrowsWhenStreamParameterIsTrue(): void
    {
        $client = (new Factory())
            ->withApiKey('sk-or-test')
            ->withHttpClient(new RecordingHttpClient())
            ->make();

        $this->expectException(InvalidArgumentException::class);
        $client->chat()->send([
            'model' => 'openai/gpt-4o',
            'messages' => [['role' => 'user', 'content' => 'hi']],
            'stream' => true,
        ]);
    }

    public function testSendStreamedInjectsStreamParameterAndParsesChunks(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueStream(ChatStreamFixture::sseBody());

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        /** @var StreamResponse<ChatStreamChunk> $stream */
        $stream = $client->chat()->sendStreamed([
            'model' => 'openai/gpt-4o',
            'messages' => [['role' => 'user', 'content' => 'hi']],
        ]);

        $request = $http->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/chat/completions', (string) $request->getUri());
        $body = json_decode((string) $request->getBody(), true);
        $this->assertTrue($body['stream']);

        $chunks = [];
        foreach ($stream as $chunk) {
            $chunks[] = $chunk;
        }

        $this->assertCount(3, $chunks);
        $this->assertContainsOnlyInstancesOf(ChatStreamChunk::class, $chunks);

        $this->assertSame('assistant', $chunks[0]->choices[0]->delta->role);
        $this->assertSame('Hello', $chunks[0]->choices[0]->delta->content);
        $this->assertNull($chunks[0]->choices[0]->finishReason);

        $this->assertSame(' world', $chunks[1]->choices[0]->delta->content);

        $this->assertSame('stop', $chunks[2]->choices[0]->finishReason);
        $this->assertNotNull($chunks[2]->usage);
        $this->assertSame(12, $chunks[2]->usage->totalTokens);
    }

    public function testSendStreamedAccumulatesToolCallArguments(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueStream(ChatStreamToolCallFixture::sseBody());

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $stream = $client->chat()->sendStreamed([
            'model' => 'openai/gpt-4o',
            'messages' => [['role' => 'user', 'content' => 'weather in Paris?']],
        ]);

        $args = '';
        $name = null;
        $finalReason = null;

        foreach ($stream as $chunk) {
            foreach ($chunk->choices as $choice) {
                if ($choice->delta->toolCalls !== null) {
                    foreach ($choice->delta->toolCalls as $tc) {
                        if ($tc->functionName !== null) {
                            $name = $tc->functionName;
                        }
                        if ($tc->functionArguments !== null) {
                            $args .= $tc->functionArguments;
                        }
                    }
                }
                if ($choice->finishReason !== null) {
                    $finalReason = $choice->finishReason;
                }
            }
        }

        $this->assertSame('get_weather', $name);
        $this->assertSame('{"location":"Paris"}', $args);
        $this->assertSame('tool_calls', $finalReason);
    }

    public function testSendAcceptsTypedRequestBuilder(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ChatCreateFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $request = new CreateChatRequest(
            messages: [
                new SystemMessage('You are helpful.'),
                new UserMessage([new ChatTextPart('Hi')]),
            ],
            model: 'openai/gpt-4o',
            temperature: 0.5,
        );

        $response = $client->chat()->send($request);

        $this->assertInstanceOf(ChatResult::class, $response);

        $body = json_decode((string) $http->lastRequest()->getBody(), true);
        $this->assertSame('openai/gpt-4o', $body['model']);
        $this->assertSame(0.5, $body['temperature']);
        $this->assertSame('system', $body['messages'][0]['role']);
        $this->assertSame('You are helpful.', $body['messages'][0]['content']);
        $this->assertSame('user', $body['messages'][1]['role']);
        $this->assertSame('text', $body['messages'][1]['content'][0]['type']);
        $this->assertSame('Hi', $body['messages'][1]['content'][0]['text']);
    }

    public function testSendStreamedAcceptsTypedRequestBuilder(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueStream(ChatStreamFixture::sseBody());

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $request = new CreateChatRequest(
            messages: [new UserMessage('Hi')],
            model: 'openai/gpt-4o',
        );

        $stream = $client->chat()->sendStreamed($request);

        $chunks = [];
        foreach ($stream as $chunk) {
            $chunks[] = $chunk;
        }

        $body = json_decode((string) $http->lastRequest()->getBody(), true);
        $this->assertSame('openai/gpt-4o', $body['model']);
        $this->assertTrue($body['stream'], 'Streamable trait must inject stream=true even for VO input');
        $this->assertCount(3, $chunks);
    }

    public function testSendThrowsWhenStreamParameterIsTrueOnTypedRequest(): void
    {
        $client = (new Factory())
            ->withApiKey('sk-or-test')
            ->withHttpClient(new RecordingHttpClient())
            ->make();

        $request = new CreateChatRequest(
            messages: [new UserMessage('Hi')],
            model: 'openai/gpt-4o',
            stream: true,
        );

        $this->expectException(InvalidArgumentException::class);
        $client->chat()->send($request);
    }

    public function testSendRaisesErrorExceptionOnApiError(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(
            ['error' => ['code' => 401, 'message' => 'Missing Authentication header']],
            401,
        );

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        try {
            $client->chat()->send([
                'model' => 'openai/gpt-4o',
                'messages' => [['role' => 'user', 'content' => 'hi']],
            ]);
            $this->fail('Expected ErrorException');
        } catch (ErrorException $e) {
            $this->assertSame('Missing Authentication header', $e->getMessage());
            $this->assertSame(401, $e->getStatusCode());
        }
    }
}
