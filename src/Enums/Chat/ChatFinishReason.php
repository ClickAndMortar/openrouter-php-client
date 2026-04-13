<?php

declare(strict_types=1);

namespace OpenRouter\Enums\Chat;

/**
 * Allowed values for `finish_reason` on a chat completion choice. Mirrors the
 * `ChatFinishReasonEnum` schema. The spec marks the field nullable and tolerates
 * unknown values.
 */
enum ChatFinishReason: string
{
    case Stop = 'stop';
    case Length = 'length';
    case ToolCalls = 'tool_calls';
    case ContentFilter = 'content_filter';
    case Error = 'error';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $c): string => $c->value, self::cases());
    }
}
