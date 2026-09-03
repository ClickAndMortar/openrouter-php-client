<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Videos;

/**
 * A video generation model and the parameter ranges it accepts. Use it to
 * decide which resolution, aspect ratio and duration to request.
 */
final class VideoModel
{
    /**
     * @param  list<string>|null  $supportedResolutions
     * @param  list<string>|null  $supportedAspectRatios
     * @param  list<string>|null  $supportedSizes
     * @param  list<int>|null  $supportedDurations
     * @param  list<string>|null  $supportedFrameImages
     * @param  list<string>|null  $allowedPassthroughParameters
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly string $id,
        public readonly string $canonicalSlug,
        public readonly string $name,
        public readonly ?string $description,
        public readonly int $created,
        public readonly ?array $supportedResolutions,
        public readonly ?array $supportedAspectRatios,
        public readonly ?array $supportedSizes,
        public readonly ?array $supportedDurations,
        public readonly ?array $supportedFrameImages,
        public readonly ?array $allowedPassthroughParameters,
        public readonly ?bool $generateAudio,
        public readonly ?bool $seed,
        public readonly array $extras = [],
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $extras = array_diff_key($attributes, array_flip([
            'id', 'canonical_slug', 'name', 'description', 'created',
            'supported_resolutions', 'supported_aspect_ratios', 'supported_sizes',
            'supported_durations', 'supported_frame_images',
            'allowed_passthrough_parameters', 'generate_audio', 'seed',
        ]));

        $strings = static function (mixed $value): ?array {
            return is_array($value) ? array_values(array_map('strval', $value)) : null;
        };

        /** @var list<int>|null $durations */
        $durations = is_array($attributes['supported_durations'] ?? null)
            ? array_values(array_map('intval', $attributes['supported_durations']))
            : null;

        return new self(
            id: is_string($attributes['id'] ?? null) ? $attributes['id'] : '',
            canonicalSlug: is_string($attributes['canonical_slug'] ?? null) ? $attributes['canonical_slug'] : '',
            name: is_string($attributes['name'] ?? null) ? $attributes['name'] : '',
            description: is_string($attributes['description'] ?? null) ? $attributes['description'] : null,
            created: is_int($attributes['created'] ?? null) ? $attributes['created'] : 0,
            supportedResolutions: $strings($attributes['supported_resolutions'] ?? null),
            supportedAspectRatios: $strings($attributes['supported_aspect_ratios'] ?? null),
            supportedSizes: $strings($attributes['supported_sizes'] ?? null),
            supportedDurations: $durations,
            supportedFrameImages: $strings($attributes['supported_frame_images'] ?? null),
            allowedPassthroughParameters: $strings($attributes['allowed_passthrough_parameters'] ?? null),
            generateAudio: isset($attributes['generate_audio']) ? (bool) $attributes['generate_audio'] : null,
            seed: isset($attributes['seed']) ? (bool) $attributes['seed'] : null,
            extras: $extras,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'id' => $this->id,
            'canonical_slug' => $this->canonicalSlug,
            'name' => $this->name,
            'created' => $this->created,
        ];

        foreach ([
            'description' => $this->description,
            'supported_resolutions' => $this->supportedResolutions,
            'supported_aspect_ratios' => $this->supportedAspectRatios,
            'supported_sizes' => $this->supportedSizes,
            'supported_durations' => $this->supportedDurations,
            'supported_frame_images' => $this->supportedFrameImages,
            'allowed_passthrough_parameters' => $this->allowedPassthroughParameters,
            'generate_audio' => $this->generateAudio,
            'seed' => $this->seed,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return [...$data, ...$this->extras];
    }
}
