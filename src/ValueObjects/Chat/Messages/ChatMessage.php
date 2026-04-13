<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat\Messages;

/**
 * Contract for a single entry in a chat completion request's `messages` array.
 * Concrete implementations correspond to the role discriminator on the
 * `ChatMessages` schema (system, user, assistant, tool, developer). Unknown
 * roles fall back to {@see UnknownMessage}.
 */
interface ChatMessage
{
    /**
     * The role discriminator (e.g. `user`, `assistant`).
     */
    public function role(): string;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
