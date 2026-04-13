<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Input\Content;

/**
 * One item in a reasoning `summary` array. The spec only defines the
 * `summary_text` shape today.
 */
final class ReasoningSummaryItem
{
    public function __construct(
        public readonly string $text,
        public readonly string $type = 'summary_text',
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            text: is_string($attributes['text'] ?? null) ? $attributes['text'] : '',
            type: is_string($attributes['type'] ?? null) ? $attributes['type'] : 'summary_text',
        );
    }

    /**
     * @return array{type: string, text: string}
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'text' => $this->text,
        ];
    }
}
