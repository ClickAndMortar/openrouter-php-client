<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Plugins;

/**
 * Auto-router plugin — lets OpenRouter pick a model from `allowed_models`
 * (supports wildcards like `anthropic/*`).
 */
final class AutoRouterPlugin implements Plugin
{
    /**
     * @param  list<string>|null  $allowedModels
     */
    public function __construct(
        public readonly ?bool $enabled = null,
        public readonly ?array $allowedModels = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $models = isset($attributes['allowed_models']) && is_array($attributes['allowed_models'])
            ? array_values(array_map('strval', $attributes['allowed_models']))
            : null;

        return new self(
            enabled: isset($attributes['enabled']) ? (bool) $attributes['enabled'] : null,
            allowedModels: $models,
        );
    }

    public function id(): string
    {
        return 'auto-router';
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
        if ($this->allowedModels !== null) {
            $data['allowed_models'] = $this->allowedModels;
        }

        return $data;
    }
}
