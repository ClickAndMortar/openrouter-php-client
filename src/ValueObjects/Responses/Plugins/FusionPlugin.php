<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Plugins;

/**
 * Fusion plugin - runs a panel of analysis models behind a single request and
 * synthesises one answer.
 */
final class FusionPlugin implements Plugin
{
    /**
     * @param  list<string>|null  $analysisModels
     * @param  list<array<string, mixed>>|null  $tools
     */
    public function __construct(
        public readonly ?bool $enabled = null,
        public readonly ?string $model = null,
        public readonly ?string $preset = null,
        public readonly ?int $maxToolCalls = null,
        public readonly ?array $analysisModels = null,
        public readonly ?array $tools = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            enabled: isset($attributes['enabled']) ? (bool) $attributes['enabled'] : null,
            model: is_string($attributes['model'] ?? null) ? $attributes['model'] : null,
            preset: is_string($attributes['preset'] ?? null) ? $attributes['preset'] : null,
            maxToolCalls: is_int($attributes['max_tool_calls'] ?? null) ? $attributes['max_tool_calls'] : null,
            analysisModels: isset($attributes['analysis_models']) && is_array($attributes['analysis_models'])
                ? array_values(array_map('strval', $attributes['analysis_models']))
                : null,
            tools: isset($attributes['tools']) && is_array($attributes['tools'])
                ? array_values(array_filter($attributes['tools'], 'is_array'))
                : null,
        );
    }

    public function id(): string
    {
        return 'fusion';
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

        if ($this->model !== null) {
            $data['model'] = $this->model;
        }

        if ($this->preset !== null) {
            $data['preset'] = $this->preset;
        }

        if ($this->maxToolCalls !== null) {
            $data['max_tool_calls'] = $this->maxToolCalls;
        }

        if ($this->analysisModels !== null) {
            $data['analysis_models'] = $this->analysisModels;
        }

        if ($this->tools !== null) {
            $data['tools'] = $this->tools;
        }

        return $data;
    }
}
