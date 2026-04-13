<?php

declare(strict_types=1);

namespace OpenRouter\Enums\Responses;

/**
 * Allowed values for the `service_tier` request parameter.
 */
enum ServiceTier: string
{
    case Auto = 'auto';
    case Default_ = 'default';
    case Flex = 'flex';
    case Priority = 'priority';
    case Scale = 'scale';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $c): string => $c->value, self::cases());
    }
}
