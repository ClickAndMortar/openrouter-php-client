<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat\Content;

use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * Audio input content part. Mirrors the `ChatContentAudio` schema.
 */
final class ChatAudioPart implements ChatContentPart
{
    public function __construct(
        public readonly string $data,
        public readonly string $format,
    ) {
        if ($this->data === '') {
            throw new InvalidArgumentException('ChatAudioPart::$data must not be empty');
        }

        if ($this->format === '') {
            throw new InvalidArgumentException('ChatAudioPart::$format must not be empty');
        }
    }

    public function type(): string
    {
        return 'input_audio';
    }

    /**
     * @return array{type: string, input_audio: array{data: string, format: string}}
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type(),
            'input_audio' => [
                'data' => $this->data,
                'format' => $this->format,
            ],
        ];
    }
}
