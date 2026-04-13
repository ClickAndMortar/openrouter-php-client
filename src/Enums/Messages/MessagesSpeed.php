<?php

declare(strict_types=1);

namespace OpenRouter\Enums\Messages;

/**
 * Mirrors the `AnthropicSpeed` enum used by the `/messages` request's `speed`
 * field.
 */
enum MessagesSpeed: string
{
    case Fast = 'fast';
    case Standard = 'standard';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $c): string => $c->value, self::cases());
    }
}
