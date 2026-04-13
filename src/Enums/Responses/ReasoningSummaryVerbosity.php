<?php

declare(strict_types=1);

namespace OpenRouter\Enums\Responses;

/**
 * Allowed values for the `reasoning.summary` request parameter.
 */
enum ReasoningSummaryVerbosity: string
{
    case Auto = 'auto';
    case Concise = 'concise';
    case Detailed = 'detailed';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $c): string => $c->value, self::cases());
    }
}
