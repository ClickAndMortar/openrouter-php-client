<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat\Messages;

use OpenRouter\ValueObjects\Chat\Content\ChatContentPart;
use OpenRouter\ValueObjects\Chat\Tools\ChatToolCallRequest;

/**
 * Assistant message. Mirrors `ChatAssistantMessage`. Content may be null, a
 * string, or a list of {@see ChatContentPart}. May carry `tool_calls` (the
 * model's request to invoke tools), `reasoning`, `reasoning_details`,
 * `refusal`, `images`, and `audio` echoed back from prior responses.
 */
final class AssistantMessage implements ChatMessage
{
    /**
     * @param  string|list<ChatContentPart>|null  $content
     * @param  list<ChatToolCallRequest|array<string, mixed>>|null  $toolCalls
     * @param  list<array<string, mixed>>|null  $reasoningDetails
     * @param  list<array<string, mixed>>|null  $images
     * @param  array<string, mixed>|null  $audio
     */
    public function __construct(
        public readonly string|array|null $content = null,
        public readonly ?array $toolCalls = null,
        public readonly ?string $name = null,
        public readonly ?string $reasoning = null,
        public readonly ?array $reasoningDetails = null,
        public readonly ?string $refusal = null,
        public readonly ?array $images = null,
        public readonly ?array $audio = null,
    ) {
    }

    public function role(): string
    {
        return 'assistant';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['role' => $this->role()];

        if ($this->content !== null) {
            $data['content'] = is_string($this->content)
                ? $this->content
                : array_map(static fn (ChatContentPart $part): array => $part->toArray(), $this->content);
        } else {
            $data['content'] = null;
        }

        if ($this->toolCalls !== null) {
            $data['tool_calls'] = array_map(
                static fn (ChatToolCallRequest|array $tc): array => $tc instanceof ChatToolCallRequest ? $tc->toArray() : $tc,
                $this->toolCalls,
            );
        }

        foreach ([
            'name' => $this->name,
            'reasoning' => $this->reasoning,
            'reasoning_details' => $this->reasoningDetails,
            'refusal' => $this->refusal,
            'images' => $this->images,
            'audio' => $this->audio,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return $data;
    }
}
