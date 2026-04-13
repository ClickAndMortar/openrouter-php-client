<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat\Messages;

use OpenRouter\ValueObjects\Chat\Content\ChatTextPart;

/**
 * System message for setting model behavior. Mirrors `ChatSystemMessage`.
 * Content is either a plain string or a list of {@see ChatTextPart}.
 */
final class SystemMessage implements ChatMessage
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
        return 'system';
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
