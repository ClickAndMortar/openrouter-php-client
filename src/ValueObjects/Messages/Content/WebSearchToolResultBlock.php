<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Content;

/**
 * `type: web_search_tool_result` — result of a server-side web search tool
 * call. `content` is either an array of search results or an error object —
 * kept as raw in V1.
 */
final class WebSearchToolResultBlock implements MessagesContentBlock
{
    /**
     * @param  array<string, mixed>|list<array<string, mixed>>  $content
     */
    public function __construct(
        public readonly string $toolUseId,
        public readonly array $content,
        public readonly ?MessagesCacheControl $cacheControl = null,
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
            cacheControl: isset($attributes['cache_control']) && is_array($attributes['cache_control'])
                ? MessagesCacheControl::from($attributes['cache_control'])
                : null,
        );
    }

    public function type(): string
    {
        return 'web_search_tool_result';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'type' => $this->type(),
            'tool_use_id' => $this->toolUseId,
            'content' => $this->content,
        ];

        if ($this->cacheControl !== null) {
            $data['cache_control'] = $this->cacheControl->toArray();
        }

        return $data;
    }
}
