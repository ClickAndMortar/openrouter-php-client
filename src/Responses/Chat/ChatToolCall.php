<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Chat;

/**
 * Tool call returned by the model on a chat completion. Mirrors the
 * `ChatToolCall` schema. The arguments string is always a JSON-encoded payload
 * matching the function's parameters schema.
 */
final class ChatToolCall
{
    private function __construct(
        public readonly string $id,
        public readonly string $type,
        public readonly string $functionName,
        public readonly string $functionArguments,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $function = is_array($attributes['function'] ?? null) ? $attributes['function'] : [];

        return new self(
            id: is_string($attributes['id'] ?? null) ? $attributes['id'] : '',
            type: is_string($attributes['type'] ?? null) ? $attributes['type'] : 'function',
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
            'type' => $this->type,
            'function' => [
                'name' => $this->functionName,
                'arguments' => $this->functionArguments,
            ],
        ];
    }
}
