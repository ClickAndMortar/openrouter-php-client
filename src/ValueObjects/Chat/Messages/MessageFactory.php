<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat\Messages;

use OpenRouter\ValueObjects\Chat\Content\ChatContentPart;
use OpenRouter\ValueObjects\Chat\Content\ChatTextPart;
use OpenRouter\ValueObjects\Chat\Content\ContentPartFactory;
use OpenRouter\ValueObjects\Chat\Tools\ChatToolCallRequest;

/**
 * Dispatches a raw message payload to the correct {@see ChatMessage}
 * implementation based on its `role` discriminator. Unknown roles fall back to
 * {@see UnknownMessage} for forward compatibility.
 */
final class MessageFactory
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): ChatMessage
    {
        $role = is_string($attributes['role'] ?? null) ? $attributes['role'] : '';

        return match ($role) {
            'system' => new SystemMessage(
                content: self::textContentFrom($attributes['content'] ?? ''),
                name: self::stringOrNull($attributes['name'] ?? null),
            ),
            'user' => new UserMessage(
                content: self::contentFrom($attributes['content'] ?? ''),
                name: self::stringOrNull($attributes['name'] ?? null),
            ),
            'assistant' => self::assistantFrom($attributes),
            'tool' => new ToolMessage(
                content: self::contentFrom($attributes['content'] ?? ''),
                toolCallId: is_string($attributes['tool_call_id'] ?? null) ? $attributes['tool_call_id'] : '',
            ),
            'developer' => new DeveloperMessage(
                content: self::textContentFrom($attributes['content'] ?? ''),
                name: self::stringOrNull($attributes['name'] ?? null),
            ),
            default => UnknownMessage::from($attributes),
        };
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private static function assistantFrom(array $attributes): AssistantMessage
    {
        $rawContent = $attributes['content'] ?? null;
        $content = $rawContent === null ? null : self::contentFrom($rawContent);

        $toolCalls = null;
        if (isset($attributes['tool_calls']) && is_array($attributes['tool_calls'])) {
            $toolCalls = array_values(array_map(
                static fn (array $tc): ChatToolCallRequest => ChatToolCallRequest::from($tc),
                array_filter($attributes['tool_calls'], 'is_array'),
            ));
        }

        return new AssistantMessage(
            content: $content,
            toolCalls: $toolCalls,
            name: self::stringOrNull($attributes['name'] ?? null),
            reasoning: self::stringOrNull($attributes['reasoning'] ?? null),
            reasoningDetails: isset($attributes['reasoning_details']) && is_array($attributes['reasoning_details'])
                ? $attributes['reasoning_details']
                : null,
            refusal: self::stringOrNull($attributes['refusal'] ?? null),
            images: isset($attributes['images']) && is_array($attributes['images'])
                ? $attributes['images']
                : null,
            audio: isset($attributes['audio']) && is_array($attributes['audio'])
                ? $attributes['audio']
                : null,
        );
    }

    /**
     * @param  mixed  $value
     * @return string|list<ChatContentPart>
     */
    private static function contentFrom(mixed $value): string|array
    {
        if (is_string($value)) {
            return $value;
        }

        if (! is_array($value)) {
            return '';
        }

        return array_values(array_map(
            static fn (array $part): ChatContentPart => ContentPartFactory::from($part),
            array_filter($value, 'is_array'),
        ));
    }

    /**
     * @param  mixed  $value
     * @return string|list<ChatTextPart>
     */
    private static function textContentFrom(mixed $value): string|array
    {
        if (is_string($value)) {
            return $value;
        }

        if (! is_array($value)) {
            return '';
        }

        return array_values(array_map(
            static fn (array $part): ChatTextPart => new ChatTextPart(
                text: is_string($part['text'] ?? null) ? $part['text'] : '',
            ),
            array_filter($value, 'is_array'),
        ));
    }

    private static function stringOrNull(mixed $value): ?string
    {
        return is_string($value) ? $value : null;
    }
}
