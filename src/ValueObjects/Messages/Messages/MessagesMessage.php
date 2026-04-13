<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Messages;

/**
 * Contract for a single entry in a `/messages` request's `messages` array.
 * Concrete implementations correspond to the `role` discriminator on the
 * Anthropic `MessagesMessageParam` schema (user, assistant). Unknown roles
 * fall back to {@see UnknownMessage}.
 */
interface MessagesMessage
{
    /**
     * The role discriminator (`user` or `assistant`).
     */
    public function role(): string;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
