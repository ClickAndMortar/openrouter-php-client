<?php

declare(strict_types=1);

namespace OpenRouter\Enums\Responses;

/**
 * Allowed values for the `role` field on a message item.
 */
enum MessageRole: string
{
    case User = 'user';
    case Assistant = 'assistant';
    case System = 'system';
    case Developer = 'developer';
    case Tool = 'tool';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $c): string => $c->value, self::cases());
    }
}
