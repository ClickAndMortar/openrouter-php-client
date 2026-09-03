<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Audio;

use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\ValueObjects\Responses\Config\ProviderPreferences;

/**
 * Typed builder for the JSON form of `POST /audio/transcriptions`, where the
 * audio travels inline in `input_audio`.
 *
 * To upload a file instead, use
 * `$client->audio()->transcribeFile($uploadedFile, $model, $options)`, which
 * sends the same endpoint as `multipart/form-data`.
 */
final class CreateTranscriptionRequest
{
    /**
     * @param  array<string, mixed>  $inputAudio
     * @param  list<string>|null  $timestampGranularities
     * @param  ProviderPreferences|array<string, mixed>|null  $provider
     * @param  array<string, mixed>  $extras
     */
    public function __construct(
        public readonly string $model,
        public readonly array $inputAudio,
        public readonly ?string $language = null,
        public readonly ?string $responseFormat = null,
        public readonly ?float $temperature = null,
        public readonly ?array $timestampGranularities = null,
        public readonly mixed $provider = null,
        public readonly array $extras = [],
    ) {
        if ($model === '') {
            throw new InvalidArgumentException('CreateTranscriptionRequest::$model must not be empty');
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['model' => $this->model, 'input_audio' => $this->inputAudio];

        if ($this->provider !== null) {
            $data['provider'] = $this->provider instanceof ProviderPreferences
                ? $this->provider->toArray()
                : $this->provider;
        }

        foreach ([
            'language' => $this->language,
            'response_format' => $this->responseFormat,
            'temperature' => $this->temperature,
            'timestamp_granularities' => $this->timestampGranularities,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return [...$data, ...$this->extras];
    }
}
