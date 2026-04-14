<?php

declare(strict_types=1);

namespace OpenRouter\Agent;

use Closure;
use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * User-defined function tool plus its executor. Pairs a JSON Schema describing
 * the call signature with a PHP closure the agent runner invokes when the
 * model emits a matching tool call.
 *
 * The closure receives the decoded arguments array (always an array — empty if
 * the model emitted no / malformed JSON) and an {@see AgentToolContext} carrying
 * the turn number, call id, and tool name. Any non-string return is JSON-encoded
 * before being sent back to the model.
 */
final class AgentTool
{
    private const NAME_MAX_LENGTH = 64;

    /**
     * @param  array<string, mixed>  $parameters  JSON Schema for the function arguments.
     * @param  Closure(array<string, mixed>, AgentToolContext): mixed  $execute
     */
    private function __construct(
        public readonly string $name,
        public readonly ?string $description,
        public readonly array $parameters,
        public readonly Closure $execute,
        public readonly ?bool $strict,
    ) {
        if ($this->name === '') {
            throw new InvalidArgumentException('AgentTool::$name must not be empty');
        }

        if (mb_strlen($this->name) > self::NAME_MAX_LENGTH) {
            throw new InvalidArgumentException(sprintf(
                'AgentTool::$name must be <= %d characters',
                self::NAME_MAX_LENGTH,
            ));
        }
    }

    /**
     * @param  array<string, mixed>  $parameters
     * @param  Closure(array<string, mixed>, AgentToolContext): mixed  $execute
     */
    public static function define(
        string $name,
        Closure $execute,
        ?string $description = null,
        array $parameters = ['type' => 'object', 'properties' => [], 'additionalProperties' => false],
        ?bool $strict = null,
    ): self {
        return new self(
            name: $name,
            description: $description,
            parameters: $parameters,
            execute: $execute,
            strict: $strict,
        );
    }

    /**
     * Wraps an {@see AgentToolDefinition} instance in an `AgentTool` so both
     * forms share a single internal representation inside the agent runner.
     */
    public static function fromDefinition(AgentToolDefinition $definition): self
    {
        return new self(
            name: $definition->name(),
            description: $definition->description(),
            parameters: $definition->parameters(),
            execute: Closure::fromCallable([$definition, 'execute']),
            strict: $definition->strict(),
        );
    }
}
