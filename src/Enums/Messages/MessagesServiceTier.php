<?php

declare(strict_types=1);

namespace OpenRouter\Enums\Messages;

enum MessagesServiceTier: string
{
    case Auto = 'auto';
    case StandardOnly = 'standard_only';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $c): string => $c->value, self::cases());
    }
}
