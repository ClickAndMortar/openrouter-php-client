<?php

declare(strict_types=1);

namespace OpenRouter\Enums\Responses\Tools;

/**
 * Allowed values for the `quality` field on the image-generation tool.
 */
enum ImageQuality: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
    case Auto = 'auto';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $c): string => $c->value, self::cases());
    }
}
