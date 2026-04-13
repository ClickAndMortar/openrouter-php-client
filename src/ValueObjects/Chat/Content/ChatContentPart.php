<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat\Content;

/**
 * Contract for a single entry in a chat message's `content` array. Concrete
 * implementations correspond to the discriminated union defined by the
 * OpenRouter OpenAPI `ChatContentItems` schema (text, image_url, input_audio,
 * file, video_url, input_video). Unknown discriminator values fall back to
 * {@see UnknownContentPart}.
 */
interface ChatContentPart
{
    public function type(): string;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
