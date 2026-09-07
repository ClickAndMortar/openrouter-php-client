<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Organization;

/**
 * A management key handed back exactly once.
 *
 * `$key` is plaintext and is returned only by the call that mints it, or by a
 * repeat call when no usable key ever reached the caller. A key that was
 * delivered is never retrievable and never replaced — store it on receipt.
 */
final class OrganizationManagementKey
{
    /**
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly string $name,
        public readonly string $key,
        public readonly array $extras = [],
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            name: is_string($attributes['name'] ?? null) ? $attributes['name'] : '',
            key: is_string($attributes['key'] ?? null) ? $attributes['key'] : '',
            extras: array_diff_key($attributes, array_flip(['name', 'key'])),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'key' => $this->key,
            ...$this->extras,
        ];
    }
}
