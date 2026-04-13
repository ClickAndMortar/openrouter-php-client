<?php

declare(strict_types=1);

namespace OpenRouter\Enums\Responses\Tools;

/**
 * Allowed values for the `search_context_size` field on web-search tools/plugins.
 */
enum SearchContextSize: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $c): string => $c->value, self::cases());
    }
}
