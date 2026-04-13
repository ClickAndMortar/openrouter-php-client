<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Chat\Stream;

use OpenRouter\Responses\Chat\ChatAudioOutput;

/**
 * Partial assistant message in a streaming chat completion. Mirrors
 * `ChatStreamDelta`. Every field is optional — callers should accumulate
 * deltas across frames.
 */
final class ChatStreamDelta
{
    /**
     * @param  list<ChatStreamToolCall>|null  $toolCalls
     * @param  list<array<string, mixed>>|null  $reasoningDetails
     */
    private function __construct(
        public readonly ?string $role,
        public readonly ?string $content,
        public readonly ?string $reasoning,
        public readonly ?array $reasoningDetails,
        public readonly ?string $refusal,
        public readonly ?array $toolCalls,
        public readonly ?ChatAudioOutput $audio,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $toolCalls = null;
        if (isset($attributes['tool_calls']) && is_array($attributes['tool_calls'])) {
            $toolCalls = array_values(array_map(
                static fn (array $tc): ChatStreamToolCall => ChatStreamToolCall::from($tc),
                array_filter($attributes['tool_calls'], 'is_array'),
            ));
        }

        return new self(
            role: isset($attributes['role']) && is_string($attributes['role']) ? $attributes['role'] : null,
            content: isset($attributes['content']) && is_string($attributes['content']) ? $attributes['content'] : null,
            reasoning: isset($attributes['reasoning']) && is_string($attributes['reasoning'])
                ? $attributes['reasoning']
                : null,
            reasoningDetails: isset($attributes['reasoning_details']) && is_array($attributes['reasoning_details'])
                ? $attributes['reasoning_details']
                : null,
            refusal: isset($attributes['refusal']) && is_string($attributes['refusal']) ? $attributes['refusal'] : null,
            toolCalls: $toolCalls,
            audio: isset($attributes['audio']) && is_array($attributes['audio'])
                ? ChatAudioOutput::from($attributes['audio'])
                : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];
        foreach ([
            'role' => $this->role,
            'content' => $this->content,
            'reasoning' => $this->reasoning,
            'reasoning_details' => $this->reasoningDetails,
            'refusal' => $this->refusal,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        if ($this->toolCalls !== null) {
            $data['tool_calls'] = array_map(
                static fn (ChatStreamToolCall $tc): array => $tc->toArray(),
                $this->toolCalls,
            );
        }

        if ($this->audio !== null) {
            $data['audio'] = $this->audio->toArray();
        }

        return $data;
    }
}
