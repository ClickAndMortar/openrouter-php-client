<?php

declare(strict_types=1);

namespace OpenRouter\Agent;

/**
 * Runtime context passed to an {@see AgentTool}'s `execute` closure. Lets
 * handlers see which turn / call id they're being invoked for without having
 * to thread that state themselves.
 */
final class AgentToolContext
{
    public function __construct(
        public readonly int $turn,
        public readonly string $toolCallId,
        public readonly string $toolName,
    ) {
    }
}
