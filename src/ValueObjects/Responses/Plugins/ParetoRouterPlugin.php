<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Plugins;

/**
 * Pareto router - selects the cheapest model that still clears a quality bar.
 */
final class ParetoRouterPlugin implements Plugin
{
    public function __construct(
        public readonly ?bool $enabled = null,
        public readonly ?float $maxPrice = null,
        public readonly ?float $minCodingScore = null,
        public readonly ?string $priceSource = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            enabled: isset($attributes['enabled']) ? (bool) $attributes['enabled'] : null,
            maxPrice: isset($attributes['max_price']) && is_numeric($attributes['max_price']) ? (float) $attributes['max_price'] : null,
            minCodingScore: isset($attributes['min_coding_score']) && is_numeric($attributes['min_coding_score']) ? (float) $attributes['min_coding_score'] : null,
            priceSource: is_string($attributes['price_source'] ?? null) ? $attributes['price_source'] : null,
        );
    }

    public function id(): string
    {
        return 'pareto-router';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['id' => $this->id()];

        if ($this->enabled !== null) {
            $data['enabled'] = $this->enabled;
        }

        if ($this->maxPrice !== null) {
            $data['max_price'] = $this->maxPrice;
        }

        if ($this->minCodingScore !== null) {
            $data['min_coding_score'] = $this->minCodingScore;
        }

        if ($this->priceSource !== null) {
            $data['price_source'] = $this->priceSource;
        }

        return $data;
    }
}
