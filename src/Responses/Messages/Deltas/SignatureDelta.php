<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Messages\Deltas;

/**
 * `signature_delta` — emitted after a thinking block to deliver the
 * provider-assigned signature token needed to replay the block on future
 * turns.
 */
final class SignatureDelta implements MessagesDelta
{
    public function __construct(
        public readonly string $signature,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            signature: is_string($attributes['signature'] ?? null) ? $attributes['signature'] : '',
        );
    }

    public function type(): string
    {
        return 'signature_delta';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return ['type' => $this->type(), 'signature' => $this->signature];
    }
}
