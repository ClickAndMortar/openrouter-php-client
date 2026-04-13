<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Messages;

/**
 * Mirrors Anthropic's `AnthropicContainer`: the sandbox container attached to
 * the response, carrying an `id` and an `expires_at` timestamp.
 */
final class MessagesContainer
{
    public function __construct(
        public readonly string $id,
        public readonly string $expiresAt,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            id: is_string($attributes['id'] ?? null) ? $attributes['id'] : '',
            expiresAt: is_string($attributes['expires_at'] ?? null) ? $attributes['expires_at'] : '',
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'expires_at' => $this->expiresAt,
        ];
    }
}
