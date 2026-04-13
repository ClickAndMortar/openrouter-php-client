<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Chat;

/**
 * One completion choice in a chat result. Mirrors `ChatChoice`. The
 * `finish_reason` is preserved as a raw string (the spec marks it nullable and
 * tolerates unknown values) — callers can compare against
 * {@see \OpenRouter\Enums\Chat\ChatFinishReason} cases when typed handling is
 * needed.
 */
final class ChatChoice
{
    private function __construct(
        public readonly int $index,
        public readonly ?string $finishReason,
        public readonly ChatResponseMessage $message,
        public readonly ?ChatTokenLogprobs $logprobs,
        public readonly ?string $nativeFinishReason,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $message = is_array($attributes['message'] ?? null) ? $attributes['message'] : [];

        return new self(
            index: is_int($attributes['index'] ?? null) ? $attributes['index'] : 0,
            finishReason: isset($attributes['finish_reason']) && is_string($attributes['finish_reason'])
                ? $attributes['finish_reason']
                : null,
            message: ChatResponseMessage::from($message),
            logprobs: isset($attributes['logprobs']) && is_array($attributes['logprobs'])
                ? ChatTokenLogprobs::from($attributes['logprobs'])
                : null,
            nativeFinishReason: isset($attributes['native_finish_reason']) && is_string($attributes['native_finish_reason'])
                ? $attributes['native_finish_reason']
                : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'index' => $this->index,
            'finish_reason' => $this->finishReason,
            'message' => $this->message->toArray(),
        ];

        if ($this->logprobs !== null) {
            $data['logprobs'] = $this->logprobs->toArray();
        }

        if ($this->nativeFinishReason !== null) {
            $data['native_finish_reason'] = $this->nativeFinishReason;
        }

        return $data;
    }
}
