<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Content;

/**
 * `type: image` — request-side image content. `source` is a discriminated
 * union (`base64` / `url`) and is passed through as a raw array.
 */
final class ImageBlock implements MessagesContentBlock
{
    /**
     * @param  array<string, mixed>  $source
     */
    public function __construct(
        public readonly array $source,
        public readonly ?MessagesCacheControl $cacheControl = null,
    ) {
    }

    public static function base64(string $mediaType, string $data, ?MessagesCacheControl $cacheControl = null): self
    {
        return new self(
            source: ['type' => 'base64', 'media_type' => $mediaType, 'data' => $data],
            cacheControl: $cacheControl,
        );
    }

    public static function url(string $url, ?MessagesCacheControl $cacheControl = null): self
    {
        return new self(
            source: ['type' => 'url', 'url' => $url],
            cacheControl: $cacheControl,
        );
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            source: is_array($attributes['source'] ?? null) ? $attributes['source'] : [],
            cacheControl: isset($attributes['cache_control']) && is_array($attributes['cache_control'])
                ? MessagesCacheControl::from($attributes['cache_control'])
                : null,
        );
    }

    public function type(): string
    {
        return 'image';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'type' => $this->type(),
            'source' => $this->source,
        ];

        if ($this->cacheControl !== null) {
            $data['cache_control'] = $this->cacheControl->toArray();
        }

        return $data;
    }
}
