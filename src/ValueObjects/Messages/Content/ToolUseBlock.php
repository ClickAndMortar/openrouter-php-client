<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Content;

/**
 * `type: tool_use` — the model's request to invoke a named tool with a JSON
 * `input`. Appears on both the request side (when replaying a prior
 * assistant message) and the response side (newly-generated tool calls).
 */
final class ToolUseBlock implements MessagesContentBlock
{
    /**
     * @param  array<string, mixed>|null  $input
     */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?array $input = null,
        public readonly ?MessagesCacheControl $cacheControl = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            id: is_string($attributes['id'] ?? null) ? $attributes['id'] : '',
            name: is_string($attributes['name'] ?? null) ? $attributes['name'] : '',
            input: isset($attributes['input']) && is_array($attributes['input']) ? $attributes['input'] : null,
            cacheControl: isset($attributes['cache_control']) && is_array($attributes['cache_control'])
                ? MessagesCacheControl::from($attributes['cache_control'])
                : null,
        );
    }

    public function type(): string
    {
        return 'tool_use';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'type' => $this->type(),
            'id' => $this->id,
            'name' => $this->name,
            'input' => $this->input,
        ];

        if ($this->cacheControl !== null) {
            $data['cache_control'] = $this->cacheControl->toArray();
        }

        return $data;
    }
}
