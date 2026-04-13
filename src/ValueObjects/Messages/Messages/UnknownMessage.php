<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Messages;

/**
 * Fallback for unknown/future roles. Preserves the raw payload so forward
 * compatibility is maintained if Anthropic adds new roles to the `/messages`
 * schema.
 */
final class UnknownMessage implements MessagesMessage
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    private function __construct(
        public readonly string $roleValue,
        public readonly array $attributes,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $role = isset($attributes['role']) && is_string($attributes['role']) ? $attributes['role'] : '';

        return new self($role, $attributes);
    }

    public function role(): string
    {
        return $this->roleValue;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
