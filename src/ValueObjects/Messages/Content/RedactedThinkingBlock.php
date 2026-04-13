<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Content;

/**
 * `type: redacted_thinking` — an opaque, provider-encrypted thinking block.
 * `data` is the encrypted payload that can be replayed back to the model on
 * future turns without exposing the underlying reasoning.
 */
final class RedactedThinkingBlock implements MessagesContentBlock
{
    public function __construct(
        public readonly string $data,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            data: is_string($attributes['data'] ?? null) ? $attributes['data'] : '',
        );
    }

    public function type(): string
    {
        return 'redacted_thinking';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type(),
            'data' => $this->data,
        ];
    }
}
