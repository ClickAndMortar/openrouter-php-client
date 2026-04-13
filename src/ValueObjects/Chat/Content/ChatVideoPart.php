<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat\Content;

use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * Video input content part. Mirrors the `ChatContentVideo` schema.
 */
final class ChatVideoPart implements ChatContentPart
{
    public function __construct(public readonly string $url)
    {
        if ($this->url === '') {
            throw new InvalidArgumentException('ChatVideoPart::$url must not be empty');
        }
    }

    public function type(): string
    {
        return 'video_url';
    }

    /**
     * @return array{type: string, video_url: array{url: string}}
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type(),
            'video_url' => ['url' => $this->url],
        ];
    }
}
