<?php

declare(strict_types=1);

namespace OpenRouter\Enums\Auth;

enum CodeChallengeMethod: string
{
    case S256 = 'S256';
    case Plain = 'plain';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $case): string => $case->value, self::cases());
    }
}
