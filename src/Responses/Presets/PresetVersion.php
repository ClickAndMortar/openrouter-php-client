<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Presets;

/**
 * One immutable version of a preset. `config` holds the inference settings
 * exactly as they were sent when the version was created.
 */
final class PresetVersion
{
    /**
     * @param  array<string, mixed>|null  $config
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly string $id,
        public readonly string $presetId,
        public readonly ?int $version,
        public readonly ?string $creatorId,
        public readonly ?string $systemPrompt,
        public readonly ?array $config,
        public readonly ?string $createdAt,
        public readonly ?string $updatedAt,
        public readonly array $extras = [],
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $extras = array_diff_key($attributes, array_flip([
            'id',
            'preset_id',
            'version',
            'creator_id',
            'system_prompt',
            'config',
            'created_at',
            'updated_at',
        ]));

        return new self(
            id: is_string($attributes['id'] ?? null) ? $attributes['id'] : '',
            presetId: is_string($attributes['preset_id'] ?? null) ? $attributes['preset_id'] : '',
            version: is_int($attributes['version'] ?? null) ? $attributes['version'] : null,
            creatorId: is_string($attributes['creator_id'] ?? null) ? $attributes['creator_id'] : null,
            systemPrompt: is_string($attributes['system_prompt'] ?? null) ? $attributes['system_prompt'] : null,
            config: is_array($attributes['config'] ?? null) ? $attributes['config'] : null,
            createdAt: is_string($attributes['created_at'] ?? null) ? $attributes['created_at'] : null,
            updatedAt: is_string($attributes['updated_at'] ?? null) ? $attributes['updated_at'] : null,
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
            'id' => $this->id,
            'preset_id' => $this->presetId,
            'version' => $this->version,
            'creator_id' => $this->creatorId,
            'system_prompt' => $this->systemPrompt,
            'config' => $this->config,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return [...$data, ...$this->extras];
    }
}
