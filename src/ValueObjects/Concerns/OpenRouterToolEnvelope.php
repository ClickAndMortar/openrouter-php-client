<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Concerns;

use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * Shared `{type, parameters}` envelope used by every OpenRouter-hosted server
 * tool, on `/responses`, `/chat/completions` and `/messages` alike.
 *
 * The using class declares the `TYPES` constant listing the discriminators it
 * accepts for its endpoint, plus named constructors for discoverability.
 */
trait OpenRouterToolEnvelope
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function __construct(
        private readonly string $type,
        public readonly array $parameters = [],
    ) {
        if (! in_array($type, static::TYPES, true)) {
            throw new InvalidArgumentException(sprintf(
                'Unknown OpenRouter server tool "%s". Expected one of: %s.',
                $type,
                implode(', ', static::TYPES),
            ));
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): static
    {
        return new static(
            is_string($attributes['type'] ?? null) ? $attributes['type'] : '',
            is_array($attributes['parameters'] ?? null) ? $attributes['parameters'] : [],
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
        $data = ['type' => $this->type];

        if ($this->parameters !== []) {
            $data['parameters'] = $this->parameters;
        }

        return $data;
    }
}
