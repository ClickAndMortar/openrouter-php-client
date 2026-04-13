<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Config;

/**
 * Typed builder for the `provider` request field. Mirrors the
 * `ProviderPreferences` schema. Complex sub-objects (`max_price`,
 * `preferred_min_throughput`, `preferred_max_latency`, `sort`) are passed
 * through as opaque arrays since they are themselves nested discriminated
 * unions where the upstream surface evolves quickly.
 */
final class ProviderPreferences
{
    public const DATA_COLLECTION_VALUES = ['allow', 'deny'];

    /**
     * @param  list<string>|null  $order
     * @param  list<string>|null  $only
     * @param  list<string>|null  $ignore
     * @param  array<string, mixed>|null  $maxPrice
     * @param  list<string>|null  $quantizations
     * @param  array<string, mixed>|string|null  $sort
     * @param  array<string, mixed>|null  $preferredMinThroughput
     * @param  array<string, mixed>|null  $preferredMaxLatency
     */
    public function __construct(
        public readonly ?array $order = null,
        public readonly ?array $only = null,
        public readonly ?array $ignore = null,
        public readonly ?bool $allowFallbacks = null,
        public readonly ?string $dataCollection = null,
        public readonly ?array $maxPrice = null,
        public readonly ?array $quantizations = null,
        public readonly array|string|null $sort = null,
        public readonly ?bool $requireParameters = null,
        public readonly ?array $preferredMinThroughput = null,
        public readonly ?array $preferredMaxLatency = null,
        public readonly ?bool $enforceDistillableText = null,
        public readonly ?bool $zdr = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            order: isset($attributes['order']) && is_array($attributes['order']) ? array_values(array_map('strval', $attributes['order'])) : null,
            only: isset($attributes['only']) && is_array($attributes['only']) ? array_values(array_map('strval', $attributes['only'])) : null,
            ignore: isset($attributes['ignore']) && is_array($attributes['ignore']) ? array_values(array_map('strval', $attributes['ignore'])) : null,
            allowFallbacks: isset($attributes['allow_fallbacks']) ? (bool) $attributes['allow_fallbacks'] : null,
            dataCollection: isset($attributes['data_collection']) && is_string($attributes['data_collection']) ? $attributes['data_collection'] : null,
            maxPrice: isset($attributes['max_price']) && is_array($attributes['max_price']) ? $attributes['max_price'] : null,
            quantizations: isset($attributes['quantizations']) && is_array($attributes['quantizations']) ? array_values(array_map('strval', $attributes['quantizations'])) : null,
            sort: isset($attributes['sort']) && (is_array($attributes['sort']) || is_string($attributes['sort'])) ? $attributes['sort'] : null,
            requireParameters: isset($attributes['require_parameters']) ? (bool) $attributes['require_parameters'] : null,
            preferredMinThroughput: isset($attributes['preferred_min_throughput']) && is_array($attributes['preferred_min_throughput']) ? $attributes['preferred_min_throughput'] : null,
            preferredMaxLatency: isset($attributes['preferred_max_latency']) && is_array($attributes['preferred_max_latency']) ? $attributes['preferred_max_latency'] : null,
            enforceDistillableText: isset($attributes['enforce_distillable_text']) ? (bool) $attributes['enforce_distillable_text'] : null,
            zdr: isset($attributes['zdr']) ? (bool) $attributes['zdr'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $optional = [
            'order' => $this->order,
            'only' => $this->only,
            'ignore' => $this->ignore,
            'allow_fallbacks' => $this->allowFallbacks,
            'data_collection' => $this->dataCollection,
            'max_price' => $this->maxPrice,
            'quantizations' => $this->quantizations,
            'sort' => $this->sort,
            'require_parameters' => $this->requireParameters,
            'preferred_min_throughput' => $this->preferredMinThroughput,
            'preferred_max_latency' => $this->preferredMaxLatency,
            'enforce_distillable_text' => $this->enforceDistillableText,
            'zdr' => $this->zdr,
        ];

        $data = [];
        foreach ($optional as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return $data;
    }
}
