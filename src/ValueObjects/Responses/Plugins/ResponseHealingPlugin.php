<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Plugins;

/**
 * Response-healing plugin. Optionally specifies a `model` to use for the
 * healing pass.
 */
final class ResponseHealingPlugin implements Plugin
{
    public function __construct(
        public readonly ?string $model = null,
        public readonly ?bool $enabled = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            model: isset($attributes['model']) && is_string($attributes['model']) ? $attributes['model'] : null,
            enabled: isset($attributes['enabled']) ? (bool) $attributes['enabled'] : null,
        );
    }

    public function id(): string
    {
        return 'response-healing';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['id' => $this->id()];

        if ($this->model !== null) {
            $data['model'] = $this->model;
        }
        if ($this->enabled !== null) {
            $data['enabled'] = $this->enabled;
        }

        return $data;
    }
}
