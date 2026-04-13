<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Chat;

/**
 * Assistant message returned on a non-streaming chat completion. Mirrors
 * `ChatAssistantMessage`. The `content` field is preserved as-is to support
 * both string and content-part-array payloads; arrays are returned unparsed.
 */
final class ChatResponseMessage
{
    /**
     * @param  string|list<array<string, mixed>>|null  $content
     * @param  list<ChatToolCall>  $toolCalls
     * @param  list<array<string, mixed>>|null  $reasoningDetails
     * @param  list<array<string, mixed>>|null  $images
     */
    private function __construct(
        public readonly string $role,
        public readonly string|array|null $content,
        public readonly array $toolCalls,
        public readonly ?string $reasoning,
        public readonly ?array $reasoningDetails,
        public readonly ?string $refusal,
        public readonly ?array $images,
        public readonly ?ChatAudioOutput $audio,
        public readonly ?string $name,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $rawContent = $attributes['content'] ?? null;
        $content = null;
        if (is_string($rawContent)) {
            $content = $rawContent;
        } elseif (is_array($rawContent)) {
            $content = array_values(array_filter($rawContent, 'is_array'));
        }

        $toolCalls = [];
        if (isset($attributes['tool_calls']) && is_array($attributes['tool_calls'])) {
            foreach ($attributes['tool_calls'] as $tc) {
                if (is_array($tc)) {
                    $toolCalls[] = ChatToolCall::from($tc);
                }
            }
        }

        return new self(
            role: is_string($attributes['role'] ?? null) ? $attributes['role'] : 'assistant',
            content: $content,
            toolCalls: $toolCalls,
            reasoning: isset($attributes['reasoning']) && is_string($attributes['reasoning'])
                ? $attributes['reasoning']
                : null,
            reasoningDetails: isset($attributes['reasoning_details']) && is_array($attributes['reasoning_details'])
                ? $attributes['reasoning_details']
                : null,
            refusal: isset($attributes['refusal']) && is_string($attributes['refusal'])
                ? $attributes['refusal']
                : null,
            images: isset($attributes['images']) && is_array($attributes['images']) ? $attributes['images'] : null,
            audio: isset($attributes['audio']) && is_array($attributes['audio'])
                ? ChatAudioOutput::from($attributes['audio'])
                : null,
            name: isset($attributes['name']) && is_string($attributes['name']) ? $attributes['name'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['role' => $this->role];

        $data['content'] = $this->content;

        if ($this->toolCalls !== []) {
            $data['tool_calls'] = array_map(static fn (ChatToolCall $tc): array => $tc->toArray(), $this->toolCalls);
        }

        foreach ([
            'reasoning' => $this->reasoning,
            'reasoning_details' => $this->reasoningDetails,
            'refusal' => $this->refusal,
            'images' => $this->images,
            'name' => $this->name,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        if ($this->audio !== null) {
            $data['audio'] = $this->audio->toArray();
        }

        return $data;
    }
}
