<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses\Stream;

use OpenRouter\Responses\Responses\CreateStreamedResponse;
use OpenRouter\Responses\Responses\Stream\Concerns\ItemScopedEvent;

/**
 * `response.web_search_call.completed` — a web search tool invocation has
 * finished. Results are carried on the corresponding output item
 * (see {@see \OpenRouter\Responses\Responses\CreateResponseOutputWebSearchCall::$action}).
 */
final class CreateStreamedWebSearchCompletedEvent extends CreateStreamedResponse
{
    use ItemScopedEvent;

    public const EVENT_TYPE = 'response.web_search_call.completed';
}
