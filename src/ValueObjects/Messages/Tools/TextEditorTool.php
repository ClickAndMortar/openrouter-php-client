<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Tools;

use OpenRouter\ValueObjects\Messages\Content\MessagesCacheControl;

/**
 * Anthropic text editor tool (`type: text_editor_20250124`,
 * `name: str_replace_editor`).
 */
final class TextEditorTool implements MessagesTool
{
    public function __construct(
        public readonly ?MessagesCacheControl $cacheControl = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            cacheControl: isset($attributes['cache_control']) && is_array($attributes['cache_control'])
                ? MessagesCacheControl::from($attributes['cache_control'])
                : null,
        );
    }

    public function type(): string
    {
        return 'text_editor_20250124';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'type' => $this->type(),
            'name' => 'str_replace_editor',
        ];

        if ($this->cacheControl !== null) {
            $data['cache_control'] = $this->cacheControl->toArray();
        }

        return $data;
    }
}
