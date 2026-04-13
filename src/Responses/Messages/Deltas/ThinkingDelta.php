<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Messages\Deltas;

final class ThinkingDelta implements MessagesDelta
{
    public function __construct(
        public readonly string $thinking,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            thinking: is_string($attributes['thinking'] ?? null) ? $attributes['thinking'] : '',
        );
    }

    public function type(): string
    {
        return 'thinking_delta';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return ['type' => $this->type(), 'thinking' => $this->thinking];
    }
}
