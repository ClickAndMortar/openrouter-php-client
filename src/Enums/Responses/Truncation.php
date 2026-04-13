<?php

declare(strict_types=1);

namespace OpenRouter\Enums\Responses;

/**
 * Allowed values for the `truncation` request parameter.
 */
enum Truncation: string
{
    case Auto = 'auto';
    case Disabled = 'disabled';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $c): string => $c->value, self::cases());
    }
}
