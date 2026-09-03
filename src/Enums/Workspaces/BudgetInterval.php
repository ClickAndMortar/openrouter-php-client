<?php

declare(strict_types=1);

namespace OpenRouter\Enums\Workspaces;

/**
 * Reset interval for a workspace budget. A workspace holds at most one budget
 * per interval, so the interval doubles as the budget's identifier in the URL.
 */
enum BudgetInterval: string
{
    case Daily = 'daily';
    case Weekly = 'weekly';
    case Monthly = 'monthly';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $c): string => $c->value, self::cases());
    }
}
