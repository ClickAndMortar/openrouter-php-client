<?php

declare(strict_types=1);

namespace OpenRouter\Agent;

use OpenRouter\Responses\Chat\ChatResult;
use OpenRouter\Responses\Chat\ChatToolCall;
use OpenRouter\Responses\Responses\CreateResponse;
use OpenRouter\Responses\Responses\CreateResponseOutputFunctionCall;

/**
 * Outcome of a completed {@see ChatAgent::run()} or
 * {@see ResponsesAgent::run()}. Gives direct access to the final text, all
 * steps, and the last wire response for escape-hatch use.
 */
final class AgentRunResult
{
    /**
     * @param  list<AgentStep>  $steps
     */
    public function __construct(
        public readonly ChatResult|CreateResponse $finalResponse,
        public readonly array $steps,
        public readonly bool $stoppedOnMaxRounds,
    ) {
    }

    public function text(): ?string
    {
        return $this->finalResponse->text();
    }

    /**
     * Tool calls on the *final* response — typically empty unless the run
     * stopped on `maxToolRounds` with outstanding tool calls.
     *
     * @return list<ChatToolCall|CreateResponseOutputFunctionCall>
     */
    public function toolCalls(): array
    {
        return $this->finalResponse->toolCalls();
    }

    /**
     * @return list<AgentStep>
     */
    public function steps(): array
    {
        return $this->steps;
    }
}
