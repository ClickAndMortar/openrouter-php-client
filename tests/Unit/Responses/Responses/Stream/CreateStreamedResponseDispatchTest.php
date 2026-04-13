<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Responses\Responses\Stream;

use OpenRouter\Responses\Responses\CreateStreamedResponse;
use OpenRouter\Responses\Responses\Stream\CreateStreamedCompletedEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedContentPartAddedEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedContentPartDoneEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedCreatedEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedErrorEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedFailedEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedFunctionCallArgumentsDeltaEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedFunctionCallArgumentsDoneEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedImageGenerationCompletedEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedImageGenerationGeneratingEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedImageGenerationInProgressEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedImageGenerationPartialImageEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedIncompleteEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedInProgressEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedOutputItemAddedEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedOutputItemDoneEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedOutputTextAnnotationAddedEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedOutputTextDeltaEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedOutputTextDoneEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedReasoningSummaryPartAddedEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedReasoningSummaryPartDoneEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedReasoningSummaryTextDeltaEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedReasoningSummaryTextDoneEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedReasoningTextDeltaEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedReasoningTextDoneEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedRefusalDeltaEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedRefusalDoneEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedWebSearchCompletedEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedWebSearchInProgressEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedWebSearchSearchingEvent;
use PHPUnit\Framework\TestCase;

final class CreateStreamedResponseDispatchTest extends TestCase
{
    /**
     * @return array<string, array{0: string, 1: class-string, 2: array<string, mixed>}>
     */
    public static function eventTypeProvider(): array
    {
        return [
            'response.created' => [
                'response.created',
                CreateStreamedCreatedEvent::class,
                ['sequence_number' => 1],
            ],
            'response.in_progress' => [
                'response.in_progress',
                CreateStreamedInProgressEvent::class,
                ['sequence_number' => 2],
            ],
            'response.completed' => [
                'response.completed',
                CreateStreamedCompletedEvent::class,
                ['sequence_number' => 99],
            ],
            'response.incomplete' => [
                'response.incomplete',
                CreateStreamedIncompleteEvent::class,
                ['sequence_number' => 3],
            ],
            'response.failed' => [
                'response.failed',
                CreateStreamedFailedEvent::class,
                ['sequence_number' => 4],
            ],
            'error' => [
                'error',
                CreateStreamedErrorEvent::class,
                ['message' => 'boom', 'code' => 'rate_limit_exceeded', 'sequence_number' => 5],
            ],
            'response.output_item.added' => [
                'response.output_item.added',
                CreateStreamedOutputItemAddedEvent::class,
                [
                    'item' => ['id' => 'msg-1', 'type' => 'message', 'role' => 'assistant', 'status' => 'in_progress', 'content' => []],
                    'output_index' => 0,
                    'sequence_number' => 6,
                ],
            ],
            'response.output_item.done' => [
                'response.output_item.done',
                CreateStreamedOutputItemDoneEvent::class,
                [
                    'item' => ['id' => 'msg-1', 'type' => 'message', 'role' => 'assistant', 'status' => 'completed', 'content' => []],
                    'output_index' => 0,
                    'sequence_number' => 7,
                ],
            ],
            'response.content_part.added' => [
                'response.content_part.added',
                CreateStreamedContentPartAddedEvent::class,
                [
                    'content_index' => 0,
                    'item_id' => 'msg-1',
                    'output_index' => 0,
                    'part' => ['type' => 'output_text', 'text' => '', 'annotations' => []],
                    'sequence_number' => 8,
                ],
            ],
            'response.content_part.done' => [
                'response.content_part.done',
                CreateStreamedContentPartDoneEvent::class,
                [
                    'content_index' => 0,
                    'item_id' => 'msg-1',
                    'output_index' => 0,
                    'part' => ['type' => 'output_text', 'text' => 'Hello', 'annotations' => []],
                    'sequence_number' => 12,
                ],
            ],
            'response.output_text.delta' => [
                'response.output_text.delta',
                CreateStreamedOutputTextDeltaEvent::class,
                [
                    'delta' => 'Hello',
                    'content_index' => 0,
                    'item_id' => 'msg-1',
                    'output_index' => 0,
                    'sequence_number' => 9,
                ],
            ],
            'response.output_text.done' => [
                'response.output_text.done',
                CreateStreamedOutputTextDoneEvent::class,
                [
                    'text' => 'Hello!',
                    'content_index' => 0,
                    'item_id' => 'msg-1',
                    'output_index' => 0,
                    'sequence_number' => 11,
                ],
            ],
            'response.output_text.annotation.added' => [
                'response.output_text.annotation.added',
                CreateStreamedOutputTextAnnotationAddedEvent::class,
                [
                    'annotation' => ['type' => 'url_citation', 'url' => 'https://example.com', 'start_index' => 0, 'end_index' => 5],
                    'annotation_index' => 0,
                    'content_index' => 0,
                    'item_id' => 'msg-1',
                    'output_index' => 0,
                    'sequence_number' => 10,
                ],
            ],
            'response.refusal.delta' => [
                'response.refusal.delta',
                CreateStreamedRefusalDeltaEvent::class,
                [
                    'delta' => 'Sorry',
                    'content_index' => 0,
                    'item_id' => 'msg-1',
                    'output_index' => 0,
                    'sequence_number' => 13,
                ],
            ],
            'response.refusal.done' => [
                'response.refusal.done',
                CreateStreamedRefusalDoneEvent::class,
                [
                    'refusal' => 'Sorry, I cannot help with that.',
                    'content_index' => 0,
                    'item_id' => 'msg-1',
                    'output_index' => 0,
                    'sequence_number' => 14,
                ],
            ],
            'response.function_call_arguments.delta' => [
                'response.function_call_arguments.delta',
                CreateStreamedFunctionCallArgumentsDeltaEvent::class,
                [
                    'delta' => '{"city":',
                    'item_id' => 'fc-1',
                    'output_index' => 0,
                    'sequence_number' => 15,
                ],
            ],
            'response.function_call_arguments.done' => [
                'response.function_call_arguments.done',
                CreateStreamedFunctionCallArgumentsDoneEvent::class,
                [
                    'arguments' => '{"city":"Paris"}',
                    'name' => 'get_weather',
                    'item_id' => 'fc-1',
                    'output_index' => 0,
                    'sequence_number' => 16,
                ],
            ],
            'response.reasoning_text.delta' => [
                'response.reasoning_text.delta',
                CreateStreamedReasoningTextDeltaEvent::class,
                ['delta' => 'Thinking', 'item_id' => 'r-1', 'output_index' => 0, 'sequence_number' => 17],
            ],
            'response.reasoning_text.done' => [
                'response.reasoning_text.done',
                CreateStreamedReasoningTextDoneEvent::class,
                ['reasoning_text' => 'Thinking...', 'item_id' => 'r-1', 'output_index' => 0, 'sequence_number' => 18],
            ],
            'response.reasoning_summary_part.added' => [
                'response.reasoning_summary_part.added',
                CreateStreamedReasoningSummaryPartAddedEvent::class,
                [
                    'part' => ['type' => 'summary_text', 'text' => 'Plan'],
                    'item_id' => 'r-1',
                    'output_index' => 0,
                    'summary_index' => 0,
                    'sequence_number' => 19,
                ],
            ],
            'response.reasoning_summary_part.done' => [
                'response.reasoning_summary_part.done',
                CreateStreamedReasoningSummaryPartDoneEvent::class,
                [
                    'part' => ['type' => 'summary_text', 'text' => 'Plan'],
                    'item_id' => 'r-1',
                    'output_index' => 0,
                    'summary_index' => 0,
                    'sequence_number' => 20,
                ],
            ],
            'response.reasoning_summary_text.delta' => [
                'response.reasoning_summary_text.delta',
                CreateStreamedReasoningSummaryTextDeltaEvent::class,
                ['delta' => 'Pl', 'item_id' => 'r-1', 'output_index' => 0, 'summary_index' => 0, 'sequence_number' => 21],
            ],
            'response.reasoning_summary_text.done' => [
                'response.reasoning_summary_text.done',
                CreateStreamedReasoningSummaryTextDoneEvent::class,
                ['text' => 'Plan', 'item_id' => 'r-1', 'output_index' => 0, 'summary_index' => 0, 'sequence_number' => 22],
            ],
            'response.image_generation_call.in_progress' => [
                'response.image_generation_call.in_progress',
                CreateStreamedImageGenerationInProgressEvent::class,
                ['item_id' => 'img-1', 'output_index' => 0, 'sequence_number' => 23],
            ],
            'response.image_generation_call.generating' => [
                'response.image_generation_call.generating',
                CreateStreamedImageGenerationGeneratingEvent::class,
                ['item_id' => 'img-1', 'output_index' => 0, 'sequence_number' => 24],
            ],
            'response.image_generation_call.partial_image' => [
                'response.image_generation_call.partial_image',
                CreateStreamedImageGenerationPartialImageEvent::class,
                [
                    'partial_image_b64' => 'AAAA',
                    'partial_image_index' => 0,
                    'item_id' => 'img-1',
                    'output_index' => 0,
                    'sequence_number' => 25,
                ],
            ],
            'response.image_generation_call.completed' => [
                'response.image_generation_call.completed',
                CreateStreamedImageGenerationCompletedEvent::class,
                ['item_id' => 'img-1', 'output_index' => 0, 'sequence_number' => 26],
            ],
            'response.web_search_call.in_progress' => [
                'response.web_search_call.in_progress',
                CreateStreamedWebSearchInProgressEvent::class,
                ['item_id' => 'ws-1', 'output_index' => 0, 'sequence_number' => 27],
            ],
            'response.web_search_call.searching' => [
                'response.web_search_call.searching',
                CreateStreamedWebSearchSearchingEvent::class,
                ['item_id' => 'ws-1', 'output_index' => 0, 'sequence_number' => 28],
            ],
            'response.web_search_call.completed' => [
                'response.web_search_call.completed',
                CreateStreamedWebSearchCompletedEvent::class,
                ['item_id' => 'ws-1', 'output_index' => 0, 'sequence_number' => 29],
            ],
        ];
    }

    /**
     * @param  class-string  $expectedClass
     * @param  array<string, mixed>  $extras
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('eventTypeProvider')]
    public function testDispatchesToCorrectSubclass(string $type, string $expectedClass, array $extras): void
    {
        $payload = ['type' => $type, ...$extras];
        $event = CreateStreamedResponse::from($payload);

        $this->assertInstanceOf($expectedClass, $event);
        $this->assertSame($type, $event->type);
        $this->assertSame($payload, $event->attributes);
    }

    public function testErrorEventCapturesMetadataAndType(): void
    {
        $payload = [
            'type' => 'error',
            'message' => 'upstream busted',
            'code' => 'rate_limit',
            'param' => null,
            'sequence_number' => 99,
            'error_type' => 'rate_limit_error',
            'metadata' => ['provider_name' => 'anthropic'],
        ];

        $event = CreateStreamedResponse::from($payload);

        $this->assertInstanceOf(CreateStreamedErrorEvent::class, $event);
        /** @var CreateStreamedErrorEvent $event */
        $this->assertSame('upstream busted', $event->message);
        $this->assertSame('rate_limit', $event->code);
        $this->assertSame(99, $event->sequenceNumber);
        $this->assertSame('rate_limit_error', $event->errorType);
        $this->assertSame(['provider_name' => 'anthropic'], $event->metadata);
    }

    public function testUnknownTypeFallsBackToBaseClass(): void
    {
        $payload = ['type' => 'response.some.future.event', 'whatever' => 42];
        $event = CreateStreamedResponse::from($payload);

        $this->assertSame(CreateStreamedResponse::class, $event::class);
        $this->assertSame('response.some.future.event', $event->type);
        $this->assertSame($payload, $event->attributes);
    }

    public function testMissingTypeFallsBackToBaseClass(): void
    {
        $event = CreateStreamedResponse::from(['whatever' => 42]);

        $this->assertSame(CreateStreamedResponse::class, $event::class);
        $this->assertNull($event->type);
    }

    public function testCompletedEventHydratesNestedResponse(): void
    {
        $payload = [
            'type' => 'response.completed',
            'sequence_number' => 99,
            'response' => [
                'id' => 'resp-1',
                'object' => 'response',
                'created_at' => 1704067200,
                'model' => 'openai/gpt-4o',
                'status' => 'completed',
                'output' => [],
                'usage' => [
                    'input_tokens' => 10,
                    'input_tokens_details' => ['cached_tokens' => 0],
                    'output_tokens' => 20,
                    'output_tokens_details' => ['reasoning_tokens' => 0],
                    'total_tokens' => 30,
                ],
                'error' => null,
                'incomplete_details' => null,
                'instructions' => null,
                'max_output_tokens' => null,
                'metadata' => null,
                'parallel_tool_calls' => true,
                'temperature' => null,
                'tool_choice' => 'auto',
                'tools' => [],
                'top_p' => null,
                'service_tier' => null,
            ],
        ];

        $event = CreateStreamedResponse::from($payload);
        $this->assertInstanceOf(CreateStreamedCompletedEvent::class, $event);
        $this->assertNotNull($event->response);
        $this->assertSame('resp-1', $event->response->id);
        $this->assertNotNull($event->response->usage);
        $this->assertSame(10, $event->response->usage->inputTokens);
        $this->assertSame(30, $event->response->usage->totalTokens);
        $this->assertSame(99, $event->sequenceNumber);
    }

    public function testCompletedEventSurvivesMalformedNestedResponse(): void
    {
        $payload = [
            'type' => 'response.completed',
            'sequence_number' => 99,
            'response' => ['missing' => 'required fields'],
        ];

        $event = CreateStreamedResponse::from($payload);
        $this->assertInstanceOf(CreateStreamedCompletedEvent::class, $event);
        $this->assertNull($event->response, 'Hydration failure should degrade to null, not throw.');
    }
}
