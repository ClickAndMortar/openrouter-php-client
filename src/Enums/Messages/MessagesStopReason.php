<?php

declare(strict_types=1);

namespace OpenRouter\Enums\Messages;

/**
 * Mirrors the `ORAnthropicStopReason` discriminator. Not enforced on
 * {@see \OpenRouter\Responses\Messages\MessagesResult::$stopReason} (kept as
 * `?string` for forward compatibility), but exposed for call sites that want
 * to compare against a typed value.
 */
enum MessagesStopReason: string
{
    case EndTurn = 'end_turn';
    case MaxTokens = 'max_tokens';
    case StopSequence = 'stop_sequence';
    case ToolUse = 'tool_use';
    case PauseTurn = 'pause_turn';
    case Refusal = 'refusal';
    case Compaction = 'compaction';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $c): string => $c->value, self::cases());
    }
}
