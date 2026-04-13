<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Messages;

use OpenRouter\ValueObjects\Messages\Content\ContentBlockFactory;
use OpenRouter\ValueObjects\Messages\Content\MessagesContentBlock;

/**
 * Dispatches a raw message payload to the correct {@see MessagesMessage}
 * implementation based on its `role` discriminator. Unknown roles fall back to
 * {@see UnknownMessage} for forward compatibility.
 */
final class MessageFactory
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): MessagesMessage
    {
        $role = is_string($attributes['role'] ?? null) ? $attributes['role'] : '';
        $content = self::contentFrom($attributes['content'] ?? '');

        return match ($role) {
            'user' => new UserMessage($content),
            'assistant' => new AssistantMessage($content),
            default => UnknownMessage::from($attributes),
        };
    }

    /**
     * @return string|list<MessagesContentBlock>
     */
    private static function contentFrom(mixed $value): string|array
    {
        if (is_string($value)) {
            return $value;
        }

        if (! is_array($value)) {
            return '';
        }

        return ContentBlockFactory::fromList($value);
    }
}
