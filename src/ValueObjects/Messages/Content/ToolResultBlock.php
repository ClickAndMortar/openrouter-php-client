<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Content;

/**
 * `type: tool_result` — request-side payload replaying a tool-call result
 * back to the model. `content` may be either a plain string or a list of
 * nested content blocks (text, image).
 */
final class ToolResultBlock implements MessagesContentBlock
{
    /**
     * @param  string|list<array<string, mixed>>  $content
     */
    public function __construct(
        public readonly string $toolUseId,
        public readonly string|array $content,
        public readonly ?bool $isError = null,
        public readonly ?MessagesCacheControl $cacheControl = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $rawContent = $attributes['content'] ?? '';
        if (is_string($rawContent)) {
            $content = $rawContent;
        } elseif (is_array($rawContent)) {
            $content = array_values(array_filter($rawContent, 'is_array'));
        } else {
            $content = '';
        }

        return new self(
            toolUseId: is_string($attributes['tool_use_id'] ?? null) ? $attributes['tool_use_id'] : '',
            content: $content,
            isError: isset($attributes['is_error']) ? (bool) $attributes['is_error'] : null,
            cacheControl: isset($attributes['cache_control']) && is_array($attributes['cache_control'])
                ? MessagesCacheControl::from($attributes['cache_control'])
                : null,
        );
    }

    public function type(): string
    {
        return 'tool_result';
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

        if ($this->isError !== null) {
            $data['is_error'] = $this->isError;
        }

        if ($this->cacheControl !== null) {
            $data['cache_control'] = $this->cacheControl->toArray();
        }

        return $data;
    }
}
