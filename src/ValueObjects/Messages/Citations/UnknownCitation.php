<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Citations;

final class UnknownCitation implements MessagesCitation
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
