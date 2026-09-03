<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Plugins;

/**
 * Beta variant of the auto-router. Picks a model under a cost tier and a
 * cost/quality tradeoff, honouring the allow and exclude lists.
 */
final class AutoBetaRouterPlugin implements Plugin
{
    /**
     * @param  list<string>|null  $allowedModels
     * @param  list<string>|null  $excludedModels
     */
    public function __construct(
        public readonly ?bool $enabled = null,
        public readonly ?string $costTier = null,
        public readonly ?int $costQualityTradeoff = null,
        public readonly ?array $allowedModels = null,
        public readonly ?array $excludedModels = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            enabled: isset($attributes['enabled']) ? (bool) $attributes['enabled'] : null,
            costTier: is_string($attributes['cost_tier'] ?? null) ? $attributes['cost_tier'] : null,
            costQualityTradeoff: is_int($attributes['cost_quality_tradeoff'] ?? null) ? $attributes['cost_quality_tradeoff'] : null,
            allowedModels: isset($attributes['allowed_models']) && is_array($attributes['allowed_models'])
                ? array_values(array_map('strval', $attributes['allowed_models']))
                : null,
            excludedModels: isset($attributes['excluded_models']) && is_array($attributes['excluded_models'])
                ? array_values(array_map('strval', $attributes['excluded_models']))
                : null,
        );
    }

    public function id(): string
    {
        return 'auto-beta-router';
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

        if ($this->costTier !== null) {
            $data['cost_tier'] = $this->costTier;
        }

        if ($this->costQualityTradeoff !== null) {
            $data['cost_quality_tradeoff'] = $this->costQualityTradeoff;
        }

        if ($this->allowedModels !== null) {
            $data['allowed_models'] = $this->allowedModels;
        }

        if ($this->excludedModels !== null) {
            $data['excluded_models'] = $this->excludedModels;
        }

        return $data;
    }
}
