<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat\Config;

use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * JSON Schema response format for structured outputs. Mirrors
 * `ChatFormatJsonSchemaConfig` (which wraps `ChatJsonSchemaConfig`).
 */
final class JsonSchemaResponseFormat implements ResponseFormat
{
    private const NAME_MAX_LENGTH = 64;

    /**
     * @param  array<string, mixed>  $schema
     */
    public function __construct(
        public readonly string $name,
        public readonly array $schema = [],
        public readonly ?string $description = null,
        public readonly ?bool $strict = null,
    ) {
        if ($this->name === '') {
            throw new InvalidArgumentException('JsonSchemaResponseFormat::$name must not be empty');
        }

        if (mb_strlen($this->name) > self::NAME_MAX_LENGTH) {
            throw new InvalidArgumentException(sprintf(
                'JsonSchemaResponseFormat::$name must be <= %d characters',
                self::NAME_MAX_LENGTH,
            ));
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $config = is_array($attributes['json_schema'] ?? null) ? $attributes['json_schema'] : [];

        return new self(
            name: is_string($config['name'] ?? null) ? $config['name'] : '',
            schema: is_array($config['schema'] ?? null) ? $config['schema'] : [],
            description: isset($config['description']) && is_string($config['description'])
                ? $config['description']
                : null,
            strict: isset($config['strict']) ? (bool) $config['strict'] : null,
        );
    }

    public function type(): string
    {
        return 'json_schema';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $config = [
            'name' => $this->name,
            'schema' => $this->schema,
        ];

        if ($this->description !== null) {
            $config['description'] = $this->description;
        }
        if ($this->strict !== null) {
            $config['strict'] = $this->strict;
        }

        return [
            'type' => $this->type(),
            'json_schema' => $config,
        ];
    }
}
