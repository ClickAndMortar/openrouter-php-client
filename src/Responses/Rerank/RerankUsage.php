<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Rerank;

/**
 * @phpstan-type RerankUsageType array{
 *     search_units: int,
 *     total_tokens: int,
 *     cost?: float,
 * }
 */
final class RerankUsage
{
    private function __construct(
        public readonly int $searchUnits,
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
            searchUnits: is_int($attributes['search_units'] ?? null) ? $attributes['search_units'] : 0,
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
            'search_units' => $this->searchUnits,
            'total_tokens' => $this->totalTokens,
        ];

        if ($this->cost !== null) {
            $data['cost'] = $this->cost;
        }

        return $data;
    }
}
