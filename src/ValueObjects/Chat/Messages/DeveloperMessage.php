<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat\Messages;

use OpenRouter\ValueObjects\Chat\Content\ChatTextPart;

/**
 * Developer message. Mirrors `ChatDeveloperMessage`. Content is either a
 * string or a list of {@see ChatTextPart}.
 */
final class DeveloperMessage implements ChatMessage
{
    /**
     * @param  string|list<ChatTextPart>  $content
     */
    public function __construct(
        public readonly string|array $content,
        public readonly ?string $name = null,
    ) {
    }

    public function role(): string
    {
        return 'developer';
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
                : array_map(static fn (ChatTextPart $part): array => $part->toArray(), $this->content),
        ];

        if ($this->name !== null) {
            $data['name'] = $this->name;
        }

        return $data;
    }
}
