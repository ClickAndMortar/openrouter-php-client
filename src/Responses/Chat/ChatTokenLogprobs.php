<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Chat;

/**
 * Log probabilities for the completion. Mirrors `ChatTokenLogprobs`.
 */
final class ChatTokenLogprobs
{
    /**
     * @param  list<ChatTokenLogprob>|null  $content
     * @param  list<ChatTokenLogprob>|null  $refusal
     */
    private function __construct(
        public readonly ?array $content,
        public readonly ?array $refusal,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $content = null;
        if (isset($attributes['content']) && is_array($attributes['content'])) {
            $content = array_values(array_map(
                static fn (array $tl): ChatTokenLogprob => ChatTokenLogprob::from($tl),
                array_filter($attributes['content'], 'is_array'),
            ));
        }

        $refusal = null;
        if (isset($attributes['refusal']) && is_array($attributes['refusal'])) {
            $refusal = array_values(array_map(
                static fn (array $tl): ChatTokenLogprob => ChatTokenLogprob::from($tl),
                array_filter($attributes['refusal'], 'is_array'),
            ));
        }

        return new self(content: $content, refusal: $refusal);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'content' => $this->content === null
                ? null
                : array_map(static fn (ChatTokenLogprob $tl): array => $tl->toArray(), $this->content),
            'refusal' => $this->refusal === null
                ? null
                : array_map(static fn (ChatTokenLogprob $tl): array => $tl->toArray(), $this->refusal),
        ];
    }
}
