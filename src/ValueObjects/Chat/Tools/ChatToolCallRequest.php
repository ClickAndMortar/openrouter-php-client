<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat\Tools;

use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * A tool call attached to an {@see \OpenRouter\ValueObjects\Chat\Messages\AssistantMessage}
 * sent back to the model as part of a multi-turn conversation. Mirrors the
 * `ChatToolCall` schema. Used on the *request* side; the response-side
 * counterpart lives at {@see \OpenRouter\Responses\Chat\ChatToolCall}.
 */
final class ChatToolCallRequest
{
    public function __construct(
        public readonly string $id,
        public readonly string $functionName,
        public readonly string $functionArguments,
    ) {
        if ($this->id === '') {
            throw new InvalidArgumentException('ChatToolCallRequest::$id must not be empty');
        }

        if ($this->functionName === '') {
            throw new InvalidArgumentException('ChatToolCallRequest::$functionName must not be empty');
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $function = is_array($attributes['function'] ?? null) ? $attributes['function'] : [];

        return new self(
            id: is_string($attributes['id'] ?? null) ? $attributes['id'] : '',
            functionName: is_string($function['name'] ?? null) ? $function['name'] : '',
            functionArguments: is_string($function['arguments'] ?? null) ? $function['arguments'] : '',
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => 'function',
            'function' => [
                'name' => $this->functionName,
                'arguments' => $this->functionArguments,
            ],
        ];
    }
}
