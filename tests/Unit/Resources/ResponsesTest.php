<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Resources;

use OpenRouter\Exceptions\ErrorException;
use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\Factory;
use OpenRouter\Responses\Responses\CreateResponse;
use OpenRouter\Responses\Responses\CreateStreamedResponse;
use OpenRouter\Responses\Responses\Stream\CreateStreamedCreatedEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedOutputTextDeltaEvent;
use OpenRouter\Responses\StreamResponse;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use OpenRouter\Tests\Fixtures\ResponsesCreateFixture;
use OpenRouter\ValueObjects\Responses\CreateResponseRequest;
use OpenRouter\ValueObjects\Responses\Input\Content\InputTextPart;
use OpenRouter\ValueObjects\Responses\Input\InputMessage;
use PHPUnit\Framework\TestCase;

final class ResponsesTest extends TestCase
{
    public function testSendHitsResponsesEndpointAsPost(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ResponsesCreateFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $client->responses()->send(['model' => 'openai/gpt-4o', 'input' => 'Tell me a joke']);

        $request = $http->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/responses', (string) $request->getUri());
        $this->assertSame('application/json', $request->getHeaderLine('Content-Type'));

        $body = json_decode((string) $request->getBody(), true);
        $this->assertSame('openai/gpt-4o', $body['model']);
        $this->assertSame('Tell me a joke', $body['input']);
    }

    public function testSendReturnsTypedCreateResponse(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ResponsesCreateFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $response = $client->responses()->send(['model' => 'openai/gpt-4o', 'input' => 'hi']);

        $this->assertInstanceOf(CreateResponse::class, $response);
        $this->assertSame('resp-abc123', $response->id);
        $this->assertSame('response', $response->object);
        $this->assertSame('openai/gpt-4o', $response->model);
        $this->assertSame('completed', $response->status);
        $this->assertSame(1704067200, $response->createdAt);

        $this->assertCount(1, $response->output);
        $message = $response->output[0];
        $this->assertSame('msg-abc123', $message->id);
        $this->assertSame('assistant', $message->role);
        $this->assertSame('message', $message->type);
        $this->assertSame('completed', $message->status);

        $this->assertCount(1, $message->content);
        $this->assertSame('output_text', $message->content[0]->type);
        $this->assertSame('Hello! How can I help you today?', $message->content[0]->text);

        $this->assertNotNull($response->usage);
        $this->assertSame(10, $response->usage->inputTokens);
        $this->assertSame(25, $response->usage->outputTokens);
        $this->assertSame(35, $response->usage->totalTokens);
        $this->assertSame(0, $response->usage->cachedTokens);
        $this->assertSame(0.0012, $response->usage->cost);

        $this->assertNull($response->error);
        $this->assertNull($response->incompleteDetails);
    }

    public function testSendThrowsWhenStreamParameterIsTrue(): void
    {
        $client = (new Factory())
            ->withApiKey('sk-or-test')
            ->withHttpClient(new RecordingHttpClient())
            ->make();

        $this->expectException(InvalidArgumentException::class);
        $client->responses()->send(['model' => 'openai/gpt-4o', 'input' => 'hi', 'stream' => true]);
    }

    public function testSendStreamedInjectsStreamParameterAndParsesSseEvents(): void
    {
        // Frames follow the flat spec shape (openapi l.11253): top-level
        // `type`, `delta`, `sequence_number`, etc. — not the nested-`data`
        // shape an older version of this test assumed.
        $sse = implode("\n", [
            'data: '.json_encode([
                'type' => 'response.created',
                'sequence_number' => 1,
            ]),
            'data: '.json_encode([
                'type' => 'response.output_text.delta',
                'sequence_number' => 2,
                'delta' => 'Hello',
                'content_index' => 0,
                'item_id' => 'msg-1',
                'output_index' => 0,
            ]),
            'data: [DONE]',
            '',
        ]);

        $http = new RecordingHttpClient();
        $http->enqueueStream($sse);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        /** @var StreamResponse<CreateStreamedResponse> $stream */
        $stream = $client->responses()->sendStreamed(['model' => 'openai/gpt-4o', 'input' => 'hi']);

        $request = $http->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $body = json_decode((string) $request->getBody(), true);
        $this->assertTrue($body['stream']);

        $events = [];
        foreach ($stream as $event) {
            $events[] = $event;
        }

        $this->assertCount(2, $events);
        $this->assertInstanceOf(CreateStreamedCreatedEvent::class, $events[0]);
        $this->assertSame('response.created', $events[0]->type);

        $this->assertInstanceOf(CreateStreamedOutputTextDeltaEvent::class, $events[1]);
        $this->assertSame('response.output_text.delta', $events[1]->type);
        $this->assertSame('Hello', $events[1]->delta);
        $this->assertSame('Hello', $events[1]->attributes['delta'], 'Legacy raw access still works');
    }

    public function testSendAcceptsTypedRequestBuilder(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ResponsesCreateFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $request = new CreateResponseRequest(
            model: 'openai/gpt-4o',
            input: [InputMessage::user([new InputTextPart('Hi')])],
            temperature: 0.5,
        );

        $response = $client->responses()->send($request);

        $this->assertInstanceOf(CreateResponse::class, $response);

        $body = json_decode((string) $http->lastRequest()->getBody(), true);
        $this->assertSame('openai/gpt-4o', $body['model']);
        $this->assertSame(0.5, $body['temperature']);
        $this->assertSame('message', $body['input'][0]['type']);
        $this->assertSame('user', $body['input'][0]['role']);
        $this->assertSame('input_text', $body['input'][0]['content'][0]['type']);
        $this->assertSame('Hi', $body['input'][0]['content'][0]['text']);
    }

    public function testSendStreamedAcceptsTypedRequestBuilder(): void
    {
        $sse = implode("\n", [
            'data: '.json_encode([
                'type' => 'response.output_text.delta',
                'sequence_number' => 1,
                'delta' => 'Ok',
                'content_index' => 0,
                'item_id' => 'msg-1',
                'output_index' => 0,
            ]),
            'data: [DONE]',
            '',
        ]);

        $http = new RecordingHttpClient();
        $http->enqueueStream($sse);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $request = new CreateResponseRequest(model: 'openai/gpt-4o', input: 'Hi');
        $stream = $client->responses()->sendStreamed($request);

        $events = [];
        foreach ($stream as $event) {
            $events[] = $event;
        }

        $body = json_decode((string) $http->lastRequest()->getBody(), true);
        $this->assertSame('openai/gpt-4o', $body['model']);
        $this->assertSame('Hi', $body['input']);
        $this->assertTrue($body['stream'], 'Streamable trait must inject stream=true even for VO input');
        $this->assertCount(1, $events);
        $this->assertInstanceOf(CreateStreamedOutputTextDeltaEvent::class, $events[0]);
        $this->assertSame('Ok', $events[0]->delta);
    }

    public function testSendThrowsWhenStreamParameterIsTrueOnTypedRequest(): void
    {
        $client = (new Factory())
            ->withApiKey('sk-or-test')
            ->withHttpClient(new RecordingHttpClient())
            ->make();

        $request = new CreateResponseRequest(model: 'openai/gpt-4o', input: 'Hi', stream: true);

        $this->expectException(InvalidArgumentException::class);
        $client->responses()->send($request);
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
            $client->responses()->send(['model' => 'openai/gpt-4o', 'input' => 'hi']);
            $this->fail('Expected ErrorException');
        } catch (ErrorException $e) {
            $this->assertSame('Missing Authentication header', $e->getMessage());
            $this->assertSame(401, $e->getStatusCode());
        }
    }
}
