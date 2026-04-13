<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat\Config;

/**
 * Streaming configuration options. Mirrors `ChatStreamOptions`. The
 * `include_usage` field is documented as deprecated server-side (full usage is
 * always returned), but accepted for forward-compat.
 */
final class ChatStreamOptions
{
    public function __construct(
        public readonly ?bool $includeUsage = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            includeUsage: isset($attributes['include_usage']) ? (bool) $attributes['include_usage'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];
        if ($this->includeUsage !== null) {
            $data['include_usage'] = $this->includeUsage;
        }

        return $data;
    }
}
