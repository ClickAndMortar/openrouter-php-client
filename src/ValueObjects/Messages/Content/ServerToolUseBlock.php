<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Content;

/**
 * `type: server_tool_use` — the model's request to invoke a provider-hosted
 * server-side tool (bash, text_editor, web_search, etc.). Structurally
 * identical to {@see ToolUseBlock} but kept distinct so the discriminator is
 * preserved round-trip.
 */
final class ServerToolUseBlock implements MessagesContentBlock
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
        return 'server_tool_use';
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
