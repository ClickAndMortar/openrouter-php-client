<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat\Content;

/**
 * Dispatches a raw content-part payload to the correct {@see ChatContentPart}
 * implementation based on its `type` discriminator. Unknown types fall back to
 * {@see UnknownContentPart} for forward compatibility.
 */
final class ContentPartFactory
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): ChatContentPart
    {
        $type = is_string($attributes['type'] ?? null) ? $attributes['type'] : '';

        return match ($type) {
            'text' => new ChatTextPart(
                text: is_string($attributes['text'] ?? null) ? $attributes['text'] : '',
                cacheControl: isset($attributes['cache_control']) && is_array($attributes['cache_control'])
                    ? ChatCacheControl::from($attributes['cache_control'])
                    : null,
            ),
            'image_url' => self::imageFrom($attributes),
            'input_audio' => self::audioFrom($attributes),
            'file' => self::fileFrom($attributes),
            'video_url' => new ChatVideoPart(
                url: is_string($attributes['video_url']['url'] ?? null) ? $attributes['video_url']['url'] : '',
            ),
            default => UnknownContentPart::from($attributes),
        };
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private static function imageFrom(array $attributes): ChatImagePart
    {
        $imageUrl = is_array($attributes['image_url'] ?? null) ? $attributes['image_url'] : [];

        return new ChatImagePart(
            url: is_string($imageUrl['url'] ?? null) ? $imageUrl['url'] : '',
            detail: isset($imageUrl['detail']) && is_string($imageUrl['detail']) ? $imageUrl['detail'] : null,
        );
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private static function audioFrom(array $attributes): ChatAudioPart
    {
        $audio = is_array($attributes['input_audio'] ?? null) ? $attributes['input_audio'] : [];

        return new ChatAudioPart(
            data: is_string($audio['data'] ?? null) ? $audio['data'] : '',
            format: is_string($audio['format'] ?? null) ? $audio['format'] : '',
        );
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private static function fileFrom(array $attributes): ChatFilePart
    {
        $file = is_array($attributes['file'] ?? null) ? $attributes['file'] : [];

        return new ChatFilePart(
            fileData: isset($file['file_data']) && is_string($file['file_data']) ? $file['file_data'] : null,
            fileId: isset($file['file_id']) && is_string($file['file_id']) ? $file['file_id'] : null,
            filename: isset($file['filename']) && is_string($file['filename']) ? $file['filename'] : null,
        );
    }
}
