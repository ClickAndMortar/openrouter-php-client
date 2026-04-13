<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Chat\Stream;

use OpenRouter\Responses\Chat\ChatTokenLogprobs;

/**
 * One streaming choice in a `ChatStreamChunk`. Mirrors `ChatStreamChoice`.
 */
final class ChatStreamChoice
{
    private function __construct(
        public readonly int $index,
        public readonly ChatStreamDelta $delta,
        public readonly ?string $finishReason,
        public readonly ?ChatTokenLogprobs $logprobs,
        public readonly ?string $nativeFinishReason,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $delta = is_array($attributes['delta'] ?? null) ? $attributes['delta'] : [];

        return new self(
            index: is_int($attributes['index'] ?? null) ? $attributes['index'] : 0,
            delta: ChatStreamDelta::from($delta),
            finishReason: isset($attributes['finish_reason']) && is_string($attributes['finish_reason'])
                ? $attributes['finish_reason']
                : null,
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
            'delta' => $this->delta->toArray(),
            'finish_reason' => $this->finishReason,
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
