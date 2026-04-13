<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Content;

/**
 * Dispatches a raw content-block payload to the correct
 * {@see MessagesContentBlock} implementation based on its `type`
 * discriminator. Unknown types fall back to {@see UnknownContentBlock}.
 *
 * Covers the full union used by both the request side (user/assistant
 * message content) and the response side (MessagesResult.content and
 * content_block_start stream events).
 */
final class ContentBlockFactory
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): MessagesContentBlock
    {
        $type = is_string($attributes['type'] ?? null) ? $attributes['type'] : '';

        return match ($type) {
            'text' => TextBlock::from($attributes),
            'image' => ImageBlock::from($attributes),
            'document' => DocumentBlock::from($attributes),
            'tool_use' => ToolUseBlock::from($attributes),
            'tool_result' => ToolResultBlock::from($attributes),
            'thinking' => ThinkingBlock::from($attributes),
            'redacted_thinking' => RedactedThinkingBlock::from($attributes),
            'server_tool_use' => ServerToolUseBlock::from($attributes),
            'web_search_tool_result' => WebSearchToolResultBlock::from($attributes),
            'web_fetch_tool_result' => WebFetchToolResultBlock::from($attributes),
            'code_execution_tool_result' => CodeExecutionToolResultBlock::from($attributes),
            'bash_code_execution_tool_result' => BashCodeExecutionToolResultBlock::from($attributes),
            'text_editor_code_execution_tool_result' => TextEditorCodeExecutionToolResultBlock::from($attributes),
            'tool_search_tool_result' => ToolSearchToolResultBlock::from($attributes),
            'container_upload' => ContainerUploadBlock::from($attributes),
            'compaction' => CompactionBlock::from($attributes),
            default => UnknownContentBlock::from($attributes),
        };
    }

    /**
     * @param  list<array<string, mixed>>  $attributes
     * @return list<MessagesContentBlock>
     */
    public static function fromList(array $attributes): array
    {
        return array_values(array_map(
            static fn (array $b): MessagesContentBlock => self::from($b),
            array_filter($attributes, 'is_array'),
        ));
    }
}
