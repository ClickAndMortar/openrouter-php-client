<?php

declare(strict_types=1);

namespace OpenRouter\Agent\Exceptions;

/**
 * Thrown when the model emits a tool call whose name has no registered
 * handler on the agent.
 */
final class UnregisteredTool extends \RuntimeException
{
    public static function for(string $name): self
    {
        return new self(sprintf(
            'Model requested tool "%s" but no handler is registered on the agent.',
            $name,
        ));
    }
}
