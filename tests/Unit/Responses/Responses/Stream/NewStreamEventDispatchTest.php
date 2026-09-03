<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Responses\Responses\Stream;

use OpenRouter\Responses\Responses\CreateStreamedResponse;
use OpenRouter\Responses\Responses\Stream\CreateStreamedApplyPatchDiffDeltaEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedApplyPatchDiffDoneEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedCodeInterpreterCallCompletedEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedCodeInterpreterCallInProgressEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedCodeInterpreterCallInterpretingEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedCodeInterpreterCodeDeltaEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedCodeInterpreterCodeDoneEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedCustomToolCallInputDeltaEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedCustomToolCallInputDoneEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedDebugEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedFusionCallAnalysisCompletedEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedFusionCallAnalysisInProgressEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedFusionCallCompletedEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedFusionCallInProgressEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedFusionCallPanelAddedEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedFusionCallPanelCompletedEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedFusionCallPanelDeltaEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedFusionCallPanelFailedEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedFusionCallPanelReasoningDeltaEvent;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Covers the `/responses` stream event types added since the April 2026 spec
 * snapshot: apply-patch diffs, the code interpreter, custom tool call input,
 * the fusion panel, and the debug channel.
 */
final class NewStreamEventDispatchTest extends TestCase
{
    /**
     * @return iterable<string, array{string, class-string}>
     */
    public static function eventProvider(): iterable
    {
        $map = [
            'response.apply_patch_call_operation_diff.delta' => CreateStreamedApplyPatchDiffDeltaEvent::class,
            'response.apply_patch_call_operation_diff.done' => CreateStreamedApplyPatchDiffDoneEvent::class,
            'response.code_interpreter_call.completed' => CreateStreamedCodeInterpreterCallCompletedEvent::class,
            'response.code_interpreter_call.in_progress' => CreateStreamedCodeInterpreterCallInProgressEvent::class,
            'response.code_interpreter_call.interpreting' => CreateStreamedCodeInterpreterCallInterpretingEvent::class,
            'response.code_interpreter_call_code.delta' => CreateStreamedCodeInterpreterCodeDeltaEvent::class,
            'response.code_interpreter_call_code.done' => CreateStreamedCodeInterpreterCodeDoneEvent::class,
            'response.custom_tool_call_input.delta' => CreateStreamedCustomToolCallInputDeltaEvent::class,
            'response.custom_tool_call_input.done' => CreateStreamedCustomToolCallInputDoneEvent::class,
            'response.debug' => CreateStreamedDebugEvent::class,
            'response.fusion_call.analysis.completed' => CreateStreamedFusionCallAnalysisCompletedEvent::class,
            'response.fusion_call.analysis.in_progress' => CreateStreamedFusionCallAnalysisInProgressEvent::class,
            'response.fusion_call.completed' => CreateStreamedFusionCallCompletedEvent::class,
            'response.fusion_call.in_progress' => CreateStreamedFusionCallInProgressEvent::class,
            'response.fusion_call.panel.added' => CreateStreamedFusionCallPanelAddedEvent::class,
            'response.fusion_call.panel.completed' => CreateStreamedFusionCallPanelCompletedEvent::class,
            'response.fusion_call.panel.delta' => CreateStreamedFusionCallPanelDeltaEvent::class,
            'response.fusion_call.panel.failed' => CreateStreamedFusionCallPanelFailedEvent::class,
            'response.fusion_call.panel.reasoning.delta' => CreateStreamedFusionCallPanelReasoningDeltaEvent::class,
        ];

        foreach ($map as $type => $class) {
            yield $type => [$type, $class];
        }
    }

    /**
     * @param  class-string  $class
     */
    #[DataProvider('eventProvider')]
    public function testFactoryDispatchesToTheTypedEventClass(string $type, string $class): void
    {
        $event = CreateStreamedResponse::from(['type' => $type, 'sequence_number' => 7]);

        $this->assertInstanceOf($class, $event);
        $this->assertSame($type, $event->type);
        $this->assertSame(7, $event->sequenceNumber);
    }

    public function testApplyPatchDiffDeltaExposesTheDiffChunk(): void
    {
        $event = CreateStreamedResponse::from([
            'type' => 'response.apply_patch_call_operation_diff.delta',
            'delta' => '@@ -1 +1 @@',
            'item_id' => 'item_1',
            'output_index' => 2,
            'sequence_number' => 9,
        ]);

        $this->assertInstanceOf(CreateStreamedApplyPatchDiffDeltaEvent::class, $event);
        $this->assertSame('@@ -1 +1 @@', $event->delta);
        $this->assertSame('item_1', $event->itemId);
        $this->assertSame(2, $event->outputIndex);
    }

    public function testCodeInterpreterCodeDoneExposesTheFinalCode(): void
    {
        $event = CreateStreamedResponse::from([
            'type' => 'response.code_interpreter_call_code.done',
            'code' => 'print(1)',
            'item_id' => 'item_2',
        ]);

        $this->assertInstanceOf(CreateStreamedCodeInterpreterCodeDoneEvent::class, $event);
        $this->assertSame('print(1)', $event->code);
    }

    public function testCustomToolCallInputDoneExposesTheInput(): void
    {
        $event = CreateStreamedResponse::from([
            'type' => 'response.custom_tool_call_input.done',
            'input' => '{"q":"hi"}',
        ]);

        $this->assertInstanceOf(CreateStreamedCustomToolCallInputDoneEvent::class, $event);
        $this->assertSame('{"q":"hi"}', $event->input);
    }

    public function testFusionPanelDeltaExposesModelAndDelta(): void
    {
        $event = CreateStreamedResponse::from([
            'type' => 'response.fusion_call.panel.delta',
            'model' => 'anthropic/claude-sonnet-4',
            'delta' => 'partial',
            'item_id' => 'item_3',
        ]);

        $this->assertInstanceOf(CreateStreamedFusionCallPanelDeltaEvent::class, $event);
        $this->assertSame('anthropic/claude-sonnet-4', $event->model);
        $this->assertSame('partial', $event->delta);
    }

    public function testFusionPanelFailedExposesErrorAndStatusCode(): void
    {
        $event = CreateStreamedResponse::from([
            'type' => 'response.fusion_call.panel.failed',
            'model' => 'openai/gpt-4o',
            'error' => 'upstream timeout',
            'status_code' => 504,
        ]);

        $this->assertInstanceOf(CreateStreamedFusionCallPanelFailedEvent::class, $event);
        $this->assertSame('upstream timeout', $event->error);
        $this->assertSame(504, $event->statusCode);
    }

    public function testFusionAnalysisInProgressExposesBothModels(): void
    {
        $event = CreateStreamedResponse::from([
            'type' => 'response.fusion_call.analysis.in_progress',
            'analyst_model' => 'anthropic/claude-sonnet-4',
            'judge_model' => 'openai/gpt-4o',
        ]);

        $this->assertInstanceOf(CreateStreamedFusionCallAnalysisInProgressEvent::class, $event);
        $this->assertSame('anthropic/claude-sonnet-4', $event->analystModel);
        $this->assertSame('openai/gpt-4o', $event->judgeModel);
    }

    public function testDebugEventExposesThePayload(): void
    {
        $event = CreateStreamedResponse::from([
            'type' => 'response.debug',
            'debug' => ['upstream' => 'anthropic'],
            'sequence_number' => 1,
        ]);

        $this->assertInstanceOf(CreateStreamedDebugEvent::class, $event);
        $this->assertSame(['upstream' => 'anthropic'], $event->debug);
    }
}
