<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat\Messages;

use OpenRouter\ValueObjects\Chat\Content\ChatContentPart;

/**
 * User message. Mirrors `ChatUserMessage`. Content is either a plain string or
 * a list of {@see ChatContentPart} (text, image_url, input_audio, file,
 * video_url).
 */
final class UserMessage implements ChatMessage
{
    /**
     * @param  string|list<ChatContentPart>  $content
     */
    public function __construct(
        public readonly string|array $content,
        public readonly ?string $name = null,
    ) {
    }

    public function role(): string
    {
        return 'user';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'role' => $this->role(),
            'content' => is_string($this->content)
                ? $this->content
                : array_map(static fn (ChatContentPart $part): array => $part->toArray(), $this->content),
        ];

        if ($this->name !== null) {
            $data['name'] = $this->name;
        }

        return $data;
    }
}
