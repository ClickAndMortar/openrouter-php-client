<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Tools;

/**
 * Dispatches a raw tool payload to the correct {@see MessagesTool}
 * implementation based on its `type` discriminator. Unknown types fall back
 * to {@see UnknownMessagesTool}.
 */
final class MessagesToolFactory
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): MessagesTool
    {
        $type = is_string($attributes['type'] ?? null) ? $attributes['type'] : '';

        return match ($type) {
            'custom' => CustomTool::from($attributes),
            'bash_20250124' => BashTool::from($attributes),
            'text_editor_20250124' => TextEditorTool::from($attributes),
            'web_search_20250305', 'web_search_20260209' => WebSearchTool::from($attributes),
            'openrouter:datetime' => DatetimeTool::from($attributes),
            'openrouter:web_search' => OpenRouterWebSearchTool::from($attributes),
            default => UnknownMessagesTool::from($attributes),
        };
    }
}
