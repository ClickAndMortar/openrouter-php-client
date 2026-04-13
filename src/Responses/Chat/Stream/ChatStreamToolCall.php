<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Chat\Stream;

/**
 * Tool call delta in a streaming response. Mirrors `ChatStreamToolCall`. The
 * `arguments` string accumulates incrementally across deltas — callers must
 * concatenate per `index` to reconstruct the full JSON.
 */
final class ChatStreamToolCall
{
    private function __construct(
        public readonly int $index,
        public readonly ?string $id,
        public readonly ?string $type,
        public readonly ?string $functionName,
        public readonly ?string $functionArguments,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $function = is_array($attributes['function'] ?? null) ? $attributes['function'] : [];

        return new self(
            index: is_int($attributes['index'] ?? null) ? $attributes['index'] : 0,
            id: isset($attributes['id']) && is_string($attributes['id']) ? $attributes['id'] : null,
            type: isset($attributes['type']) && is_string($attributes['type']) ? $attributes['type'] : null,
            functionName: isset($function['name']) && is_string($function['name']) ? $function['name'] : null,
            functionArguments: isset($function['arguments']) && is_string($function['arguments'])
                ? $function['arguments']
                : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $function = [];
        if ($this->functionName !== null) {
            $function['name'] = $this->functionName;
        }
        if ($this->functionArguments !== null) {
            $function['arguments'] = $this->functionArguments;
        }

        $data = ['index' => $this->index];
        if ($this->id !== null) {
            $data['id'] = $this->id;
        }
        if ($this->type !== null) {
            $data['type'] = $this->type;
        }
        if ($function !== []) {
            $data['function'] = $function;
        }

        return $data;
    }
}
