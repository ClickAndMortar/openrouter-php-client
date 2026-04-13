<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Responses\Responses\Stream;

use OpenRouter\Factory;
use OpenRouter\Responses\Responses\CreateResponseOutputFunctionCall;
use OpenRouter\Responses\Responses\CreateStreamedResponse;
use OpenRouter\Responses\Responses\Stream\CreateStreamedCompletedEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedContentPartAddedEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedContentPartDoneEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedCreatedEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedFunctionCallArgumentsDeltaEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedFunctionCallArgumentsDoneEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedInProgressEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedOutputItemAddedEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedOutputItemDoneEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedOutputTextDeltaEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedOutputTextDoneEvent;
use OpenRouter\Responses\StreamResponse;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use OpenRouter\Tests\Fixtures\ResponsesStreamFixture;
use OpenRouter\Tests\Fixtures\ResponsesStreamFunctionCallFixture;
use PHPUnit\Framework\TestCase;

final class StreamEventEndToEndTest extends TestCase
{
    public function testTextStreamYieldsTypedEventSequence(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueStream(ResponsesStreamFixture::sseBody());

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        /** @var StreamResponse<CreateStreamedResponse> $stream */
        $stream = $client->responses()->sendStreamed([
            'model' => 'openai/gpt-4o',
            'input' => 'Say hi',
        ]);

        $events = [];
        foreach ($stream as $event) {
            $events[] = $event;
        }

        $this->assertCount(11, $events);

        $this->assertInstanceOf(CreateStreamedCreatedEvent::class, $events[0]);
        $this->assertNotNull($events[0]->response);
        $this->assertSame('resp-stream-1', $events[0]->response->id);
        $this->assertSame('in_progress', $events[0]->response->status);

        $this->assertInstanceOf(CreateStreamedInProgressEvent::class, $events[1]);

        $this->assertInstanceOf(CreateStreamedOutputItemAddedEvent::class, $events[2]);
        $this->assertNotNull($events[2]->item);
        $this->assertSame('msg-1', $events[2]->item->id());

        $this->assertInstanceOf(CreateStreamedContentPartAddedEvent::class, $events[3]);
        $this->assertSame('output_text', $events[3]->part['type']);

        $this->assertInstanceOf(CreateStreamedOutputTextDeltaEvent::class, $events[4]);
        $this->assertSame('Hello', $events[4]->delta);
        $this->assertSame(5, $events[4]->sequenceNumber);

        $this->assertInstanceOf(CreateStreamedOutputTextDeltaEvent::class, $events[5]);
        $this->assertSame(' ', $events[5]->delta);

        $this->assertInstanceOf(CreateStreamedOutputTextDeltaEvent::class, $events[6]);
        $this->assertSame('world!', $events[6]->delta);

        $this->assertInstanceOf(CreateStreamedOutputTextDoneEvent::class, $events[7]);
        $this->assertSame('Hello world!', $events[7]->text);

        $this->assertInstanceOf(CreateStreamedContentPartDoneEvent::class, $events[8]);
        $this->assertInstanceOf(CreateStreamedOutputItemDoneEvent::class, $events[9]);

        $completed = $events[10];
        $this->assertInstanceOf(CreateStreamedCompletedEvent::class, $completed);
        $this->assertNotNull($completed->response);
        $this->assertSame('completed', $completed->response->status);
        $this->assertNotNull($completed->response->usage);
        $this->assertSame(25, $completed->response->usage->outputTokens);
        $this->assertSame(35, $completed->response->usage->totalTokens);
    }

    public function testTypeAndAttributesFieldsArePopulatedForBackwardsCompat(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueStream(ResponsesStreamFixture::sseBody());

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();
        $stream = $client->responses()->sendStreamed([
            'model' => 'openai/gpt-4o',
            'input' => 'Say hi',
        ]);

        $delta = null;
        $completed = null;
        foreach ($stream as $event) {
            if ($event->type === 'response.output_text.delta' && $delta === null) {
                $delta = $event;
            }
            if ($event->type === 'response.completed') {
                $completed = $event;
            }
        }

        $this->assertNotNull($delta);
        $this->assertSame('Hello', $delta->attributes['delta'], 'Legacy $event->attributes access must still work');

        $this->assertNotNull($completed);
        $this->assertIsArray($completed->attributes['response']);
        $this->assertSame(
            35,
            $completed->attributes['response']['usage']['total_tokens'],
            'Legacy $event->attributes[response][usage] access must still work',
        );
    }

    public function testFunctionCallStreamExposesTypedDeltas(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueStream(ResponsesStreamFunctionCallFixture::sseBody());

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();
        $stream = $client->responses()->sendStreamed([
            'model' => 'openai/gpt-4o',
            'input' => 'What is the weather in Paris?',
            'tools' => [['type' => 'function', 'name' => 'get_weather']],
        ]);

        $deltas = [];
        $doneEvent = null;
        $completed = null;
        foreach ($stream as $event) {
            if ($event instanceof CreateStreamedFunctionCallArgumentsDeltaEvent) {
                $deltas[] = $event->delta;
            }
            if ($event instanceof CreateStreamedFunctionCallArgumentsDoneEvent) {
                $doneEvent = $event;
            }
            if ($event instanceof CreateStreamedCompletedEvent) {
                $completed = $event;
            }
        }

        $this->assertSame(['{"city":', '"Paris"}'], $deltas);
        $this->assertNotNull($doneEvent);
        $this->assertSame('{"city":"Paris"}', $doneEvent->arguments);
        $this->assertSame('get_weather', $doneEvent->name);

        $this->assertNotNull($completed);
        $this->assertNotNull($completed->response);
        $this->assertCount(1, $completed->response->output);
        $this->assertInstanceOf(CreateResponseOutputFunctionCall::class, $completed->response->output[0]);
        $this->assertSame('call-xyz', $completed->response->output[0]->callId);
    }
}
