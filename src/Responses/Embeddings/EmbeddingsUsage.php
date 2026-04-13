<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Embeddings;

/**
 * Token usage statistics for an embeddings request. Mirrors the `usage`
 * object on the embeddings response. `cost` is an OpenRouter-specific
 * extension and may be absent on some backends.
 *
 * @phpstan-type EmbeddingsUsageType array{
 *     prompt_tokens: int,
 *     total_tokens: int,
 *     cost?: float,
 * }
 */
final class EmbeddingsUsage
{
    private function __construct(
        public readonly int $promptTokens,
        public readonly int $totalTokens,
        public readonly ?float $cost,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            promptTokens: is_int($attributes['prompt_tokens'] ?? null) ? $attributes['prompt_tokens'] : 0,
            totalTokens: is_int($attributes['total_tokens'] ?? null) ? $attributes['total_tokens'] : 0,
            cost: isset($attributes['cost']) && is_numeric($attributes['cost']) ? (float) $attributes['cost'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'prompt_tokens' => $this->promptTokens,
            'total_tokens' => $this->totalTokens,
        ];

        if ($this->cost !== null) {
            $data['cost'] = $this->cost;
        }

        return $data;
    }
}
