<?php

declare(strict_types=1);

namespace OpenRouter\Agent;

/**
 * Class-based alternative to {@see AgentTool}. Implement this on any class you
 * want the agent runner to invoke when the model emits a matching tool call —
 * useful for stateful tools, dependency-injected services, or tools you want
 * to unit-test independently of the runner.
 *
 * Register an instance via `ChatAgent::tool()` / `ResponsesAgent::tool()`
 * just like you would an `AgentTool`; the runner normalises both forms
 * internally.
 */
interface AgentToolDefinition
{
    public function name(): string;

    public function description(): ?string;

    /**
     * JSON Schema describing the function arguments. Typically
     * `['type' => 'object', 'properties' => [...], 'required' => [...]]`.
     *
     * @return array<string, mixed>
     */
    public function parameters(): array;

    /**
     * Return null to defer to the provider default, true/false to force.
     */
    public function strict(): ?bool;

    /**
     * @param  array<string, mixed>  $arguments  JSON-decoded tool-call arguments.
     * @return mixed  Non-string returns are JSON-encoded before being sent back to the model.
     */
    public function execute(array $arguments, AgentToolContext $context): mixed;
}
