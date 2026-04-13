<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Tools;

use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * A user-defined function the model may call. The `parameters` field holds
 * the JSON Schema describing the call signature.
 */
final class FunctionTool implements Tool
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function __construct(
        public readonly string $name,
        public readonly array $parameters,
        public readonly ?string $description = null,
        public readonly ?bool $strict = null,
    ) {
        if ($this->name === '') {
            throw new InvalidArgumentException('FunctionTool::$name must not be empty');
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            name: is_string($attributes['name'] ?? null) ? $attributes['name'] : '',
            parameters: is_array($attributes['parameters'] ?? null) ? $attributes['parameters'] : [],
            description: isset($attributes['description']) && is_string($attributes['description']) ? $attributes['description'] : null,
            strict: isset($attributes['strict']) ? (bool) $attributes['strict'] : null,
        );
    }

    public function type(): string
    {
        return 'function';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'type' => $this->type(),
            'name' => $this->name,
            'parameters' => $this->parameters,
        ];

        if ($this->description !== null) {
            $data['description'] = $this->description;
        }
        if ($this->strict !== null) {
            $data['strict'] = $this->strict;
        }

        return $data;
    }
}
