<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Messages\Deltas;

final class TextDelta implements MessagesDelta
{
    public function __construct(
        public readonly string $text,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            text: is_string($attributes['text'] ?? null) ? $attributes['text'] : '',
        );
    }

    public function type(): string
    {
        return 'text_delta';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return ['type' => $this->type(), 'text' => $this->text];
    }
}
