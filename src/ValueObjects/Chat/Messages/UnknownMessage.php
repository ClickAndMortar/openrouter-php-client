<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat\Messages;

/**
 * Forward-compat fallback for messages whose `role` discriminator is not
 * recognized.
 */
final class UnknownMessage implements ChatMessage
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    private function __construct(
        public readonly string $role,
        public readonly array $attributes,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            role: is_string($attributes['role'] ?? null) ? $attributes['role'] : 'unknown',
            attributes: $attributes,
        );
    }

    public function role(): string
    {
        return $this->role;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
