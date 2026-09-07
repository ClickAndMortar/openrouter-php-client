<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Organization;

/**
 * The customer organization created by `POST /organization`, named
 * "[Parent] Customer" after the Connect-enabled organization that created it.
 */
final class CreatedOrganization
{
    /**
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly string $email,
        public readonly array $extras = [],
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
            slug: is_string($attributes['slug'] ?? null) ? $attributes['slug'] : '',
            email: is_string($attributes['email'] ?? null) ? $attributes['email'] : '',
            extras: array_diff_key($attributes, array_flip(['id', 'name', 'slug', 'email'])),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'email' => $this->email,
            ...$this->extras,
        ];
    }
}
