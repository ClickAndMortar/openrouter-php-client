<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Tools;

use OpenRouter\ValueObjects\Messages\Content\MessagesCacheControl;

/**
 * Anthropic bash tool (`type: bash_20250124`, `name: bash`). The model can
 * issue shell commands against a sandbox container.
 */
final class BashTool implements MessagesTool
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
        return 'bash_20250124';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'type' => $this->type(),
            'name' => 'bash',
        ];

        if ($this->cacheControl !== null) {
            $data['cache_control'] = $this->cacheControl->toArray();
        }

        return $data;
    }
}
