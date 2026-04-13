<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Plugins;

/**
 * Context-compression plugin. Optional `model` and `compression_level`.
 */
final class ContextCompressionPlugin implements Plugin
{
    public function __construct(
        public readonly ?string $model = null,
        public readonly ?bool $enabled = null,
        public readonly ?string $compressionLevel = null,
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
            compressionLevel: isset($attributes['compression_level']) && is_string($attributes['compression_level']) ? $attributes['compression_level'] : null,
        );
    }

    public function id(): string
    {
        return 'context-compression';
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
        if ($this->compressionLevel !== null) {
            $data['compression_level'] = $this->compressionLevel;
        }

        return $data;
    }
}
