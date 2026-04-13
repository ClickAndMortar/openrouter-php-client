<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Messages;

use OpenRouter\ValueObjects\Messages\Content\MessagesContentBlock;

/**
 * User message for Anthropic `/messages`. `content` is either a plain string
 * or a list of content blocks — each entry may be either a typed
 * {@see MessagesContentBlock} or a raw array passed through unchanged.
 */
final class UserMessage implements MessagesMessage
{
    /**
     * @param  string|list<MessagesContentBlock|array<string, mixed>>  $content
     */
    public function __construct(
        public readonly string|array $content,
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
        return [
            'role' => $this->role(),
            'content' => is_string($this->content)
                ? $this->content
                : array_map(
                    static fn (MessagesContentBlock|array $b): array => $b instanceof MessagesContentBlock
                        ? $b->toArray()
                        : $b,
                    $this->content,
                ),
        ];
    }
}
