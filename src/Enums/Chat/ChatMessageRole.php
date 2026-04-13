<?php

declare(strict_types=1);

namespace OpenRouter\Enums\Chat;

/**
 * The five roles supported by the chat completions API.
 */
enum ChatMessageRole: string
{
    case System = 'system';
    case User = 'user';
    case Assistant = 'assistant';
    case Tool = 'tool';
    case Developer = 'developer';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $c): string => $c->value, self::cases());
    }
}
