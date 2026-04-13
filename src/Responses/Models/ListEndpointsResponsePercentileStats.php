<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Models;

/**
 * @phpstan-type ListEndpointsResponsePercentileStatsType array{
 *     p50?: float|int|null,
 *     p75?: float|int|null,
 *     p90?: float|int|null,
 *     p99?: float|int|null,
 * }
 */
final class ListEndpointsResponsePercentileStats
{
    private function __construct(
        public readonly float|int|null $p50,
        public readonly float|int|null $p75,
        public readonly float|int|null $p90,
        public readonly float|int|null $p99,
    ) {
    }

    /**
     * @param  ListEndpointsResponsePercentileStatsType  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            p50: $attributes['p50'] ?? null,
            p75: $attributes['p75'] ?? null,
            p90: $attributes['p90'] ?? null,
            p99: $attributes['p99'] ?? null,
        );
    }

    /**
     * @return ListEndpointsResponsePercentileStatsType
     */
    public function toArray(): array
    {
        return [
            'p50' => $this->p50,
            'p75' => $this->p75,
            'p90' => $this->p90,
            'p99' => $this->p99,
        ];
    }
}
