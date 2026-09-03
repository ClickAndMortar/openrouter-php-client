<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses\Stream;

use OpenRouter\Responses\Responses\CreateStreamedResponse;
use OpenRouter\Responses\Responses\Stream\Concerns\ItemScopedEvent;

/**
 * `response.code_interpreter_call.in_progress` - a code interpreter tool call has started.
 */
final class CreateStreamedCodeInterpreterCallInProgressEvent extends CreateStreamedResponse
{
    use ItemScopedEvent;

    public const EVENT_TYPE = 'response.code_interpreter_call.in_progress';
}
