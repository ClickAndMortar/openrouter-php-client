<?php

declare(strict_types=1);

namespace OpenRouter\Enums\Responses;

/**
 * Allowed values for the `reasoning.effort` request parameter.
 */
enum ReasoningEffort: string
{
    case Xhigh = 'xhigh';
    case High = 'high';
    case Medium = 'medium';
    case Low = 'low';
    case Minimal = 'minimal';
    case None = 'none';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $c): string => $c->value, self::cases());
    }
}
