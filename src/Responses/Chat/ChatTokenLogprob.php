<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Chat;

/**
 * Token log probability information. Mirrors `ChatTokenLogprob`.
 */
final class ChatTokenLogprob
{
    /**
     * @param  list<int>|null  $bytes
     * @param  list<array<string, mixed>>  $topLogprobs
     */
    private function __construct(
        public readonly string $token,
        public readonly float $logprob,
        public readonly ?array $bytes,
        public readonly array $topLogprobs,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $bytes = null;
        if (isset($attributes['bytes']) && is_array($attributes['bytes'])) {
            $bytes = array_values(array_filter($attributes['bytes'], 'is_int'));
        }

        $topLogprobs = [];
        if (isset($attributes['top_logprobs']) && is_array($attributes['top_logprobs'])) {
            $topLogprobs = array_values(array_filter($attributes['top_logprobs'], 'is_array'));
        }

        return new self(
            token: is_string($attributes['token'] ?? null) ? $attributes['token'] : '',
            logprob: (float) ($attributes['logprob'] ?? 0.0),
            bytes: $bytes,
            topLogprobs: $topLogprobs,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'token' => $this->token,
            'logprob' => $this->logprob,
            'bytes' => $this->bytes,
            'top_logprobs' => $this->topLogprobs,
        ];
    }
}
