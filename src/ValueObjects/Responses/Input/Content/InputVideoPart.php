<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Input\Content;

/**
 * Video content part inside an input message. Mirrors the `InputVideo` schema
 * from the OpenRouter OpenAPI spec. `videoUrl` can be a remote URL or a base64
 * data URL (`data:video/mp4;base64,...`).
 */
final class InputVideoPart implements InputContentPart
{
    public function __construct(public readonly string $videoUrl)
    {
    }

    public function type(): string
    {
        return 'input_video';
    }

    /**
     * @return array{type: string, video_url: string}
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type(),
            'video_url' => $this->videoUrl,
        ];
    }
}
