<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Images;

use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\ValueObjects\Responses\Config\ProviderPreferences;

/**
 * Typed builder for `POST /images`.
 *
 * Which parameters a model honours varies — `$client->images()->listModels()`
 * reports `supported_parameters` per model, and `supports_streaming` says
 * whether `generateStreamed()` is available.
 */
final class CreateImageRequest
{
    /**
     * @param  list<string>|null  $inputReferences
     * @param  ProviderPreferences|array<string, mixed>|null  $provider
     * @param  array<string, mixed>  $extras
     */
    public function __construct(
        public readonly string $model,
        public readonly string $prompt,
        public readonly ?int $n = null,
        public readonly ?string $size = null,
        public readonly ?string $aspectRatio = null,
        public readonly ?string $resolution = null,
        public readonly ?string $quality = null,
        public readonly ?string $background = null,
        public readonly ?string $outputFormat = null,
        public readonly ?int $outputCompression = null,
        public readonly ?int $seed = null,
        public readonly ?string $user = null,
        public readonly ?array $inputReferences = null,
        public readonly mixed $provider = null,
        public readonly array $extras = [],
    ) {
        if ($model === '') {
            throw new InvalidArgumentException('CreateImageRequest::$model must not be empty');
        }

        if ($prompt === '') {
            throw new InvalidArgumentException('CreateImageRequest::$prompt must not be empty');
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['model' => $this->model, 'prompt' => $this->prompt];

        if ($this->provider !== null) {
            $data['provider'] = $this->provider instanceof ProviderPreferences
                ? $this->provider->toArray()
                : $this->provider;
        }

        foreach ([
            'n' => $this->n,
            'size' => $this->size,
            'aspect_ratio' => $this->aspectRatio,
            'resolution' => $this->resolution,
            'quality' => $this->quality,
            'background' => $this->background,
            'output_format' => $this->outputFormat,
            'output_compression' => $this->outputCompression,
            'seed' => $this->seed,
            'user' => $this->user,
            'input_references' => $this->inputReferences,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return [...$data, ...$this->extras];
    }
}
