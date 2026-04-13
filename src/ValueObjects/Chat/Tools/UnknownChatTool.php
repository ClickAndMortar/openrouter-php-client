<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat\Tools;

/**
 * Forward-compat fallback for chat tool entries whose `type` discriminator is
 * not recognized.
 */
final class UnknownChatTool implements ChatTool
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    private function __construct(
        public readonly string $type,
        public readonly array $attributes,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            type: is_string($attributes['type'] ?? null) ? $attributes['type'] : 'unknown',
            attributes: $attributes,
        );
    }

    public function type(): string
    {
        return $this->type;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
