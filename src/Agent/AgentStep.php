<?php

declare(strict_types=1);

namespace OpenRouter\Agent;

use OpenRouter\Responses\Chat\ChatResult;
use OpenRouter\Responses\Chat\ChatToolCall;
use OpenRouter\Responses\Responses\CreateResponse;
use OpenRouter\Responses\Responses\CreateResponseOutputFunctionCall;

/**
 * Immutable snapshot of one round-trip inside an agent run: the assistant
 * response plus the local tool executions that happened afterwards. Returned
 * as part of {@see AgentRunResult::steps()} for observability.
 */
final class AgentStep
{
    /**
     * @param  list<ChatToolCall|CreateResponseOutputFunctionCall>  $toolCalls
     * @param  array<string, mixed>  $toolResults  Keyed by tool call id; value is whatever
     *                                              the handler returned (or an error map).
     */
    public function __construct(
        public readonly int $turn,
        public readonly ChatResult|CreateResponse $response,
        public readonly array $toolCalls,
        public readonly array $toolResults,
    ) {
    }
}
