<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Tools;

/**
 * Forward-compat fallback for tool entries whose `type` discriminator is not
 * recognized. Preserves the raw payload so new Anthropic/OpenRouter tool types
 * keep working without a client upgrade.
 */
final class UnknownMessagesTool implements MessagesTool
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    private function __construct(
        public readonly string $typeValue,
        public readonly array $attributes,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            typeValue: is_string($attributes['type'] ?? null) ? $attributes['type'] : 'unknown',
            attributes: $attributes,
        );
    }

    public function type(): string
    {
        return $this->typeValue;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
