<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Videos;

use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\ValueObjects\Responses\Config\ProviderPreferences;

/**
 * Typed builder for `POST /videos`. Video generation is asynchronous: the call
 * returns a job whose `polling_url` you poll until `status` settles.
 *
 * Which parameters a model honours varies — `$client->videos()->listModels()`
 * reports the supported resolutions, aspect ratios, durations and whether the
 * model can generate audio.
 */
final class CreateVideoRequest
{
    /**
     * @param  list<string>|null  $frameImages
     * @param  list<string>|null  $inputReferences
     * @param  ProviderPreferences|array<string, mixed>|null  $provider
     * @param  array<string, mixed>  $extras
     */
    public function __construct(
        public readonly string $model,
        public readonly ?string $prompt = null,
        public readonly ?string $aspectRatio = null,
        public readonly ?string $resolution = null,
        public readonly ?string $size = null,
        public readonly ?int $duration = null,
        public readonly ?bool $generateAudio = null,
        public readonly ?int $seed = null,
        public readonly ?int $creativity = null,
        public readonly ?float $upscaleFactor = null,
        public readonly ?array $frameImages = null,
        public readonly ?array $inputReferences = null,
        public readonly ?string $callbackUrl = null,
        public readonly mixed $provider = null,
        public readonly array $extras = [],
    ) {
        if ($model === '') {
            throw new InvalidArgumentException('CreateVideoRequest::$model must not be empty');
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['model' => $this->model];

        if ($this->provider !== null) {
            $data['provider'] = $this->provider instanceof ProviderPreferences
                ? $this->provider->toArray()
                : $this->provider;
        }

        foreach ([
            'prompt' => $this->prompt,
            'aspect_ratio' => $this->aspectRatio,
            'resolution' => $this->resolution,
            'size' => $this->size,
            'duration' => $this->duration,
            'generate_audio' => $this->generateAudio,
            'seed' => $this->seed,
            'creativity' => $this->creativity,
            'upscale_factor' => $this->upscaleFactor,
            'frame_images' => $this->frameImages,
            'input_references' => $this->inputReferences,
            'callback_url' => $this->callbackUrl,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return [...$data, ...$this->extras];
    }
}
