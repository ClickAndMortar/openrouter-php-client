<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses;

/**
 * Forward-compat fallback for output items whose `type` discriminator is not
 * recognized by this client. The full raw payload is preserved so callers can
 * still inspect it, while the rest of the `output` array remains typed.
 */
final class CreateResponseOutputUnknown implements CreateResponseOutputItem
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    private function __construct(
        public readonly string $id,
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
            id: is_string($attributes['id'] ?? null) ? $attributes['id'] : '',
            type: is_string($attributes['type'] ?? null) ? $attributes['type'] : 'unknown',
            attributes: $attributes,
        );
    }

    public function type(): string
    {
        return $this->type;
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
