<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Input\Content;

use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * Audio content part inside an input message. Mirrors the `InputAudio` schema
 * from the OpenRouter OpenAPI spec. The `data` field holds base64-encoded audio.
 */
final class InputAudioPart implements InputContentPart
{
    public function __construct(
        public readonly string $data,
        public readonly string $format,
    ) {
        if (! in_array($this->format, ['mp3', 'wav'], true)) {
            throw new InvalidArgumentException(
                sprintf('InputAudioPart::$format must be one of mp3/wav, got "%s"', $this->format),
            );
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
