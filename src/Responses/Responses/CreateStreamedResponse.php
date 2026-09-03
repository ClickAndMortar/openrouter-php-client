<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses;

use OpenRouter\Responses\Responses\Stream\CreateStreamedCodeInterpreterCallCompletedEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedCodeInterpreterCallInProgressEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedCodeInterpreterCallInterpretingEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedFusionCallCompletedEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedFusionCallInProgressEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedApplyPatchDiffDeltaEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedApplyPatchDiffDoneEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedCodeInterpreterCodeDeltaEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedCodeInterpreterCodeDoneEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedCustomToolCallInputDeltaEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedCustomToolCallInputDoneEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedFusionCallAnalysisCompletedEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedFusionCallAnalysisInProgressEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedFusionCallPanelAddedEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedFusionCallPanelCompletedEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedFusionCallPanelDeltaEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedFusionCallPanelFailedEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedFusionCallPanelReasoningDeltaEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedDebugEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedContentPartAddedEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedContentPartDoneEvent;
use OpenRouter\Responses\Responses\Stream\CreateStreamedCompletedEvent;
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

/**
 * Base class for a single Server-Sent Event frame from OpenRouter's streaming
 * `/responses` endpoint. `from()` dispatches to the concrete subclass matching
 * the event's `type` discriminator; unknown event types fall through to a
 * base-class instance so that callers keep working when the API adds new types.
 *
 * The `type` and `attributes` properties are populated for every instance,
 * including concrete subclasses, so callers that still read
 * `$event->attributes[...]` continue to work unchanged.
 *
 * @phpstan-type CreateStreamedResponseType array<string, mixed>
 */
class CreateStreamedResponse
{
    /**
     * @param  CreateStreamedResponseType  $attributes
     */
    public function __construct(
        public readonly ?string $type,
        public readonly array $attributes,
    ) {
    }

    /**
     * @param  CreateStreamedResponseType  $payload
     */
    public static function from(array $payload): self
    {
        $type = isset($payload['type']) && is_string($payload['type']) ? $payload['type'] : null;

        return match ($type) {
            'response.created' => CreateStreamedCreatedEvent::fromPayload($payload),
            'response.in_progress' => CreateStreamedInProgressEvent::fromPayload($payload),
            'response.completed' => CreateStreamedCompletedEvent::fromPayload($payload),
            'response.incomplete' => CreateStreamedIncompleteEvent::fromPayload($payload),
            'response.failed' => CreateStreamedFailedEvent::fromPayload($payload),
            'error' => CreateStreamedErrorEvent::fromPayload($payload),
            'response.code_interpreter_call.completed' => CreateStreamedCodeInterpreterCallCompletedEvent::fromPayload($payload),
            'response.code_interpreter_call.in_progress' => CreateStreamedCodeInterpreterCallInProgressEvent::fromPayload($payload),
            'response.code_interpreter_call.interpreting' => CreateStreamedCodeInterpreterCallInterpretingEvent::fromPayload($payload),
            'response.fusion_call.completed' => CreateStreamedFusionCallCompletedEvent::fromPayload($payload),
            'response.fusion_call.in_progress' => CreateStreamedFusionCallInProgressEvent::fromPayload($payload),
            'response.apply_patch_call_operation_diff.delta' => CreateStreamedApplyPatchDiffDeltaEvent::fromPayload($payload),
            'response.apply_patch_call_operation_diff.done' => CreateStreamedApplyPatchDiffDoneEvent::fromPayload($payload),
            'response.code_interpreter_call_code.delta' => CreateStreamedCodeInterpreterCodeDeltaEvent::fromPayload($payload),
            'response.code_interpreter_call_code.done' => CreateStreamedCodeInterpreterCodeDoneEvent::fromPayload($payload),
            'response.custom_tool_call_input.delta' => CreateStreamedCustomToolCallInputDeltaEvent::fromPayload($payload),
            'response.custom_tool_call_input.done' => CreateStreamedCustomToolCallInputDoneEvent::fromPayload($payload),
            'response.fusion_call.analysis.completed' => CreateStreamedFusionCallAnalysisCompletedEvent::fromPayload($payload),
            'response.fusion_call.analysis.in_progress' => CreateStreamedFusionCallAnalysisInProgressEvent::fromPayload($payload),
            'response.fusion_call.panel.added' => CreateStreamedFusionCallPanelAddedEvent::fromPayload($payload),
            'response.fusion_call.panel.completed' => CreateStreamedFusionCallPanelCompletedEvent::fromPayload($payload),
            'response.fusion_call.panel.delta' => CreateStreamedFusionCallPanelDeltaEvent::fromPayload($payload),
            'response.fusion_call.panel.failed' => CreateStreamedFusionCallPanelFailedEvent::fromPayload($payload),
            'response.fusion_call.panel.reasoning.delta' => CreateStreamedFusionCallPanelReasoningDeltaEvent::fromPayload($payload),
            'response.debug' => CreateStreamedDebugEvent::fromPayload($payload),
            'response.output_item.added' => CreateStreamedOutputItemAddedEvent::fromPayload($payload),
            'response.output_item.done' => CreateStreamedOutputItemDoneEvent::fromPayload($payload),
            'response.content_part.added' => CreateStreamedContentPartAddedEvent::fromPayload($payload),
            'response.content_part.done' => CreateStreamedContentPartDoneEvent::fromPayload($payload),
            'response.output_text.delta' => CreateStreamedOutputTextDeltaEvent::fromPayload($payload),
            'response.output_text.done' => CreateStreamedOutputTextDoneEvent::fromPayload($payload),
            'response.output_text.annotation.added' => CreateStreamedOutputTextAnnotationAddedEvent::fromPayload($payload),
            'response.refusal.delta' => CreateStreamedRefusalDeltaEvent::fromPayload($payload),
            'response.refusal.done' => CreateStreamedRefusalDoneEvent::fromPayload($payload),
            'response.function_call_arguments.delta' => CreateStreamedFunctionCallArgumentsDeltaEvent::fromPayload($payload),
            'response.function_call_arguments.done' => CreateStreamedFunctionCallArgumentsDoneEvent::fromPayload($payload),
            'response.reasoning_text.delta' => CreateStreamedReasoningTextDeltaEvent::fromPayload($payload),
            'response.reasoning_text.done' => CreateStreamedReasoningTextDoneEvent::fromPayload($payload),
            'response.reasoning_summary_part.added' => CreateStreamedReasoningSummaryPartAddedEvent::fromPayload($payload),
            'response.reasoning_summary_part.done' => CreateStreamedReasoningSummaryPartDoneEvent::fromPayload($payload),
            'response.reasoning_summary_text.delta' => CreateStreamedReasoningSummaryTextDeltaEvent::fromPayload($payload),
            'response.reasoning_summary_text.done' => CreateStreamedReasoningSummaryTextDoneEvent::fromPayload($payload),
            'response.image_generation_call.in_progress' => CreateStreamedImageGenerationInProgressEvent::fromPayload($payload),
            'response.image_generation_call.generating' => CreateStreamedImageGenerationGeneratingEvent::fromPayload($payload),
            'response.image_generation_call.partial_image' => CreateStreamedImageGenerationPartialImageEvent::fromPayload($payload),
            'response.image_generation_call.completed' => CreateStreamedImageGenerationCompletedEvent::fromPayload($payload),
            'response.web_search_call.in_progress' => CreateStreamedWebSearchInProgressEvent::fromPayload($payload),
            'response.web_search_call.searching' => CreateStreamedWebSearchSearchingEvent::fromPayload($payload),
            'response.web_search_call.completed' => CreateStreamedWebSearchCompletedEvent::fromPayload($payload),
            default => new self($type, $payload),
        };
    }

    /**
     * @return CreateStreamedResponseType
     */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
