<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Config;

/**
 * Typed builder for the `text` request field. The `format` sub-object is
 * passed through as an opaque array (it's a discriminated union over
 * `text` / `json_object` / `json_schema`).
 */
final class TextExtendedConfig
{
    /**
     * @param  array<string, mixed>|null  $format
     */
    public function __construct(
        public readonly ?array $format = null,
        public readonly ?string $verbosity = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            format: isset($attributes['format']) && is_array($attributes['format']) ? $attributes['format'] : null,
            verbosity: isset($attributes['verbosity']) && is_string($attributes['verbosity']) ? $attributes['verbosity'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];

        if ($this->format !== null) {
            $data['format'] = $this->format;
        }
        if ($this->verbosity !== null) {
            $data['verbosity'] = $this->verbosity;
        }

        return $data;
    }
}
