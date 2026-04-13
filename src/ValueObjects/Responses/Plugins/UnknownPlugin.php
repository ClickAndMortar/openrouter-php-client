<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Plugins;

/**
 * Forward-compat fallback for plugins whose `id` discriminator is not
 * recognized by this client.
 */
final class UnknownPlugin implements Plugin
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    private function __construct(
        public readonly string $id,
        public readonly array $attributes,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            id: is_string($attributes['id'] ?? null) ? $attributes['id'] : 'unknown',
            attributes: $attributes,
        );
    }

    public function id(): string
    {
        return $this->id;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
