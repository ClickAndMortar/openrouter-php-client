<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat\Tools;

/**
 * Contract for a single entry in the chat completion request's `tools` array.
 * Mirrors the discriminated union defined by `ChatFunctionTool` (function,
 * datetime, openrouter:web_search, web_search shorthand). Unknown
 * discriminators fall back to {@see UnknownChatTool}.
 */
interface ChatTool
{
    public function type(): string;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
