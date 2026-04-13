<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Tools;

/**
 * Forward-compat fallback for tool entries whose `type` discriminator is not
 * recognized by this client. Preserves the raw payload so callers can still
 * inspect or send it.
 */
final class UnknownTool implements Tool
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
