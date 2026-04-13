<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\ContextManagement;

final class ContextManagementEditFactory
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): ContextManagementEdit
    {
        $type = is_string($attributes['type'] ?? null) ? $attributes['type'] : '';

        return match ($type) {
            'clear_tool_uses_20250919' => ClearToolUsesEdit::from($attributes),
            'clear_thinking_20251015' => ClearThinkingEdit::from($attributes),
            'compact_20260112' => CompactEdit::from($attributes),
            default => UnknownContextManagementEdit::from($attributes),
        };
    }
}
