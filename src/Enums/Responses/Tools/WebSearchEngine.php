<?php

declare(strict_types=1);

namespace OpenRouter\Enums\Responses\Tools;

/**
 * Allowed values for the `engine` field on web-search tools/plugins.
 */
enum WebSearchEngine: string
{
    case Auto = 'auto';
    case Native = 'native';
    case Exa = 'exa';
    case Firecrawl = 'firecrawl';
    case Parallel = 'parallel';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $c): string => $c->value, self::cases());
    }
}
