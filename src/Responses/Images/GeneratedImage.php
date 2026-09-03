<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Images;

/**
 * One generated image. The bytes arrive base64-encoded; {@see binary()}
 * decodes them.
 */
final class GeneratedImage
{
    /**
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly string $b64Json,
        public readonly ?string $mediaType,
        public readonly array $extras = [],
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            is_string($attributes['b64_json'] ?? null) ? $attributes['b64_json'] : '',
            is_string($attributes['media_type'] ?? null) ? $attributes['media_type'] : null,
            array_diff_key($attributes, array_flip(['b64_json', 'media_type'])),
        );
    }

    /**
     * Raw image bytes, or null when the payload is not valid base64.
     */
    public function binary(): ?string
    {
        $decoded = base64_decode($this->b64Json, true);

        return $decoded === false ? null : $decoded;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['b64_json' => $this->b64Json];

        if ($this->mediaType !== null) {
            $data['media_type'] = $this->mediaType;
        }

        return [...$data, ...$this->extras];
    }
}
