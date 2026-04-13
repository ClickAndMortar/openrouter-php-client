<?php

declare(strict_types=1);

namespace OpenRouter\Enums\Responses;

/**
 * Allowed values for the `status` field on a response object.
 */
enum ResponseStatus: string
{
    case Completed = 'completed';
    case Incomplete = 'incomplete';
    case InProgress = 'in_progress';
    case Failed = 'failed';
    case Cancelled = 'cancelled';
    case Queued = 'queued';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $c): string => $c->value, self::cases());
    }
}
