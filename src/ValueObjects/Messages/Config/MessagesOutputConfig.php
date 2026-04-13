<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Config;

use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * Mirrors the `MessagesOutputConfig` schema. Carries an optional reasoning
 * `effort` level and an optional structured-output `format` (json_schema only
 * as of this schema version).
 */
final class MessagesOutputConfig
{
    public const EFFORT_LEVELS = ['low', 'medium', 'high', 'max'];

    /**
     * @param  array<string, mixed>|null  $format
     */
    public function __construct(
        public readonly ?string $effort = null,
        public readonly ?array $format = null,
    ) {
        if ($this->effort !== null && ! in_array($this->effort, self::EFFORT_LEVELS, true)) {
            throw new InvalidArgumentException(sprintf(
                'MessagesOutputConfig::$effort must be one of %s or null, got "%s"',
                implode('/', self::EFFORT_LEVELS),
                $this->effort,
            ));
        }
    }

    /**
     * @param  array<string, mixed>  $schema
     */
    public static function jsonSchema(array $schema, ?string $effort = null): self
    {
        return new self(
            effort: $effort,
            format: ['type' => 'json_schema', 'schema' => $schema],
        );
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            effort: isset($attributes['effort']) && is_string($attributes['effort']) ? $attributes['effort'] : null,
            format: isset($attributes['format']) && is_array($attributes['format']) ? $attributes['format'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];
        if ($this->effort !== null) {
            $data['effort'] = $this->effort;
        }
        if ($this->format !== null) {
            $data['format'] = $this->format;
        }

        return $data;
    }
}
