<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Tools;

use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * Custom tool. `name` is required. `format` is the output-format object
 * (text or grammar with lark/regex syntax) — passed through as an opaque
 * array since the spec models it as a discriminated union.
 */
final class CustomTool implements Tool
{
    /**
     * @param  array<string, mixed>|null  $format
     */
    public function __construct(
        public readonly string $name,
        public readonly ?string $description = null,
        public readonly ?array $format = null,
    ) {
        if ($this->name === '') {
            throw new InvalidArgumentException('CustomTool::$name must not be empty');
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            name: is_string($attributes['name'] ?? null) ? $attributes['name'] : '',
            description: isset($attributes['description']) && is_string($attributes['description']) ? $attributes['description'] : null,
            format: isset($attributes['format']) && is_array($attributes['format']) ? $attributes['format'] : null,
        );
    }

    public function type(): string
    {
        return 'custom';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'type' => $this->type(),
            'name' => $this->name,
        ];

        if ($this->description !== null) {
            $data['description'] = $this->description;
        }
        if ($this->format !== null) {
            $data['format'] = $this->format;
        }

        return $data;
    }
}
