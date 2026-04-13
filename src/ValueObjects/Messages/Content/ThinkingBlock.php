<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Content;

/**
 * `type: thinking` — an extended-thinking reasoning block. `signature` is
 * a provider-assigned token used to replay thinking blocks across turns.
 */
final class ThinkingBlock implements MessagesContentBlock
{
    public function __construct(
        public readonly string $thinking,
        public readonly string $signature,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            thinking: is_string($attributes['thinking'] ?? null) ? $attributes['thinking'] : '',
            signature: is_string($attributes['signature'] ?? null) ? $attributes['signature'] : '',
        );
    }

    public function type(): string
    {
        return 'thinking';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type(),
            'thinking' => $this->thinking,
            'signature' => $this->signature,
        ];
    }
}
