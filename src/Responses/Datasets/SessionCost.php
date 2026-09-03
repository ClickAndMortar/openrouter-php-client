<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Datasets;

/**
 * Median cost of a session for one app, model and conversation length.
 */
final class SessionCost
{
    /**
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly ?string $appSlug,
        public readonly ?string $appName,
        public readonly ?string $modelPermaslug,
        public readonly ?string $turnRange,
        public readonly ?float $medianSessionCostUsd,
        public readonly array $extras = [],
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $extras = array_diff_key($attributes, array_flip([
            'app_slug',
            'app_name',
            'model_permaslug',
            'turn_range',
            'median_session_cost_usd',
        ]));

        return new self(
            appSlug: is_string($attributes['app_slug'] ?? null) ? $attributes['app_slug'] : null,
            appName: is_string($attributes['app_name'] ?? null) ? $attributes['app_name'] : null,
            modelPermaslug: is_string($attributes['model_permaslug'] ?? null) ? $attributes['model_permaslug'] : null,
            turnRange: is_string($attributes['turn_range'] ?? null) ? $attributes['turn_range'] : null,
            medianSessionCostUsd: isset($attributes['median_session_cost_usd']) && is_numeric($attributes['median_session_cost_usd']) ? (float) $attributes['median_session_cost_usd'] : null,
            extras: $extras,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];

        foreach ([
            'app_slug' => $this->appSlug,
            'app_name' => $this->appName,
            'model_permaslug' => $this->modelPermaslug,
            'turn_range' => $this->turnRange,
            'median_session_cost_usd' => $this->medianSessionCostUsd,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return [...$data, ...$this->extras];
    }
}
