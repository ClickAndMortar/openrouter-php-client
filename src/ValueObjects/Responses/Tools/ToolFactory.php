<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Tools;

/**
 * Dispatches a raw tool payload to the correct {@see Tool} implementation
 * based on its `type` discriminator. Unknown types fall back to
 * {@see UnknownTool} for forward compatibility.
 */
final class ToolFactory
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): Tool
    {
        $type = is_string($attributes['type'] ?? null) ? $attributes['type'] : '';

        return match ($type) {
            'function' => FunctionTool::from($attributes),
            'web_search_2025_08_26' => WebSearchServerTool::from($attributes),
            'web_search_preview' => WebSearchPreviewServerTool::from($attributes),
            'web_search_preview_2025_03_11' => WebSearchPreview20250311ServerTool::from($attributes),
            'web_search' => WebSearchLegacyServerTool::from($attributes),
            'openrouter:web_search' => WebSearchServerToolOpenRouter::from($attributes),
            'file_search' => FileSearchServerTool::from($attributes),
            'mcp' => McpServerTool::from($attributes),
            'image_generation' => ImageGenerationServerTool::from($attributes),
            'code_interpreter' => CodeInterpreterServerTool::from($attributes),
            'computer_use_preview' => ComputerUseServerTool::from($attributes),
            'shell' => ShellServerTool::from($attributes),
            'local_shell' => CodexLocalShellTool::from($attributes),
            'apply_patch' => ApplyPatchServerTool::from($attributes),
            'openrouter:datetime' => DatetimeServerTool::from($attributes),
            'custom' => CustomTool::from($attributes),
            default => UnknownTool::from($attributes),
        };
    }
}
