<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Messages\Deltas;

/**
 * `input_json_delta` — incremental fragment of a tool's `input` JSON. Concat
 * the `partial_json` strings across events to reconstruct the full input.
 */
final class InputJsonDelta implements MessagesDelta
{
    public function __construct(
        public readonly string $partialJson,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            partialJson: is_string($attributes['partial_json'] ?? null) ? $attributes['partial_json'] : '',
        );
    }

    public function type(): string
    {
        return 'input_json_delta';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return ['type' => $this->type(), 'partial_json' => $this->partialJson];
    }
}
