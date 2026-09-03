<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses\Stream;

use OpenRouter\Responses\Responses\CreateStreamedResponse;
use OpenRouter\Responses\Responses\Stream\Concerns\ItemScopedEvent;

/**
 * `response.code_interpreter_call.interpreting` - the code interpreter is executing the generated code.
 */
final class CreateStreamedCodeInterpreterCallInterpretingEvent extends CreateStreamedResponse
{
    use ItemScopedEvent;

    public const EVENT_TYPE = 'response.code_interpreter_call.interpreting';
}
