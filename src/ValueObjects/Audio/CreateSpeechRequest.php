<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Audio;

use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\ValueObjects\Responses\Config\ProviderPreferences;

/**
 * Typed builder for `POST /audio/speech`. The response is a raw audio
 * bytestream whose Content-Type follows `$responseFormat`.
 */
final class CreateSpeechRequest
{
    /**
     * @param  list<string>|null  $inputReferences
     * @param  ProviderPreferences|array<string, mixed>|null  $provider
     * @param  array<string, mixed>  $extras
     */
    public function __construct(
        public readonly string $model,
        public readonly string $input,
        public readonly ?string $voice = null,
        public readonly ?string $responseFormat = null,
        public readonly ?float $speed = null,
        public readonly ?array $inputReferences = null,
        public readonly mixed $provider = null,
        public readonly array $extras = [],
    ) {
        if ($model === '') {
            throw new InvalidArgumentException('CreateSpeechRequest::$model must not be empty');
        }

        if ($input === '') {
            throw new InvalidArgumentException('CreateSpeechRequest::$input must not be empty');
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['model' => $this->model, 'input' => $this->input];

        if ($this->provider !== null) {
            $data['provider'] = $this->provider instanceof ProviderPreferences
                ? $this->provider->toArray()
                : $this->provider;
        }

        foreach ([
            'voice' => $this->voice,
            'response_format' => $this->responseFormat,
            'speed' => $this->speed,
            'input_references' => $this->inputReferences,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return [...$data, ...$this->extras];
    }
}
