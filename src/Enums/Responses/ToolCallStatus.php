<?php

declare(strict_types=1);

namespace OpenRouter\Enums\Responses;

/**
 * Allowed values for the `status` field on a tool-call output item.
 */
enum ToolCallStatus: string
{
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Incomplete = 'incomplete';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $c): string => $c->value, self::cases());
    }
}
