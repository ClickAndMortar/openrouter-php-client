<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Content;

/**
 * `type: tool_search_tool_result` — result of a tool-search server tool.
 */
final class ToolSearchToolResultBlock implements MessagesContentBlock
{
    /**
     * @param  array<string, mixed>  $content
     */
    public function __construct(
        public readonly string $toolUseId,
        public readonly array $content,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            toolUseId: is_string($attributes['tool_use_id'] ?? null) ? $attributes['tool_use_id'] : '',
            content: is_array($attributes['content'] ?? null) ? $attributes['content'] : [],
        );
    }

    public function type(): string
    {
        return 'tool_search_tool_result';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type(),
            'tool_use_id' => $this->toolUseId,
            'content' => $this->content,
        ];
    }
}
