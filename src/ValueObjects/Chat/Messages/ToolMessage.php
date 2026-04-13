<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat\Messages;

use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\ValueObjects\Chat\Content\ChatContentPart;

/**
 * Tool response message returned to the model after an assistant tool call.
 * Mirrors `ChatToolMessage`. `tool_call_id` matches the assistant's
 * preceding `ChatToolCall::id`.
 */
final class ToolMessage implements ChatMessage
{
    /**
     * @param  string|list<ChatContentPart>  $content
     */
    public function __construct(
        public readonly string|array $content,
        public readonly string $toolCallId,
    ) {
        if ($this->toolCallId === '') {
            throw new InvalidArgumentException('ToolMessage::$toolCallId must not be empty');
        }
    }

    public function role(): string
    {
        return 'tool';
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
                : array_map(static fn (ChatContentPart $part): array => $part->toArray(), $this->content),
            'tool_call_id' => $this->toolCallId,
        ];
    }
}
