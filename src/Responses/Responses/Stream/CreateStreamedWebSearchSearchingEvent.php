<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses\Stream;

use OpenRouter\Responses\Responses\CreateStreamedResponse;
use OpenRouter\Responses\Responses\Stream\Concerns\ItemScopedEvent;

/**
 * `response.web_search_call.searching` — a web search tool invocation is
 * actively querying a search engine.
 */
final class CreateStreamedWebSearchSearchingEvent extends CreateStreamedResponse
{
    use ItemScopedEvent;

    public const EVENT_TYPE = 'response.web_search_call.searching';
}
