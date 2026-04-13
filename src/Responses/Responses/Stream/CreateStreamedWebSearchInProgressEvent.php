<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses\Stream;

use OpenRouter\Responses\Responses\CreateStreamedResponse;
use OpenRouter\Responses\Responses\Stream\Concerns\ItemScopedEvent;

/**
 * `response.web_search_call.in_progress` — a web search tool invocation has
 * been scheduled and is about to start.
 */
final class CreateStreamedWebSearchInProgressEvent extends CreateStreamedResponse
{
    use ItemScopedEvent;

    public const EVENT_TYPE = 'response.web_search_call.in_progress';
}
