<?php

declare(strict_types=1);

namespace OpenRouter\Enums\Messages;

/**
 * Roles supported by the Anthropic-compatible `/messages` endpoint. Unlike
 * `/chat/completions`, `system` is a top-level field rather than a message
 * role, so only `user` and `assistant` appear inside the `messages` array.
 */
enum MessagesRole: string
{
    case User = 'user';
    case Assistant = 'assistant';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $c): string => $c->value, self::cases());
    }
}
