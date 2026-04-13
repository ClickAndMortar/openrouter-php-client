<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Chat;

/**
 * Audio output returned by audio-capable models. Mirrors `ChatAudioOutput`.
 */
final class ChatAudioOutput
{
    private function __construct(
        public readonly ?string $id,
        public readonly ?string $data,
        public readonly ?int $expiresAt,
        public readonly ?string $transcript,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            id: isset($attributes['id']) && is_string($attributes['id']) ? $attributes['id'] : null,
            data: isset($attributes['data']) && is_string($attributes['data']) ? $attributes['data'] : null,
            expiresAt: isset($attributes['expires_at']) && is_int($attributes['expires_at'])
                ? $attributes['expires_at']
                : null,
            transcript: isset($attributes['transcript']) && is_string($attributes['transcript'])
                ? $attributes['transcript']
                : null,
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
            'data' => $this->data,
            'expires_at' => $this->expiresAt,
            'transcript' => $this->transcript,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return $data;
    }
}
