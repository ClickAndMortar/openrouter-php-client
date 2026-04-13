<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses;

/**
 * Log probability information for a token produced by the model.
 *
 * @phpstan-type LogProbsType array{
 *     token: string,
 *     logprob: float,
 *     bytes?: list<int>|null,
 *     top_logprobs?: list<array<string, mixed>>,
 * }
 */
final class LogProbs
{
    /**
     * @param  list<int>|null  $bytes
     * @param  list<TopLogprobs>  $topLogprobs
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly string $token,
        public readonly float $logprob,
        public readonly ?array $bytes,
        public readonly array $topLogprobs,
        public readonly array $extras,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $known = ['token', 'logprob', 'bytes', 'top_logprobs'];
        $extras = array_diff_key($attributes, array_flip($known));

        $bytes = null;
        if (isset($attributes['bytes']) && is_array($attributes['bytes'])) {
            $bytes = array_values(array_map('intval', $attributes['bytes']));
        }

        $topLogprobs = [];
        if (isset($attributes['top_logprobs']) && is_array($attributes['top_logprobs'])) {
            foreach ($attributes['top_logprobs'] as $entry) {
                if (is_array($entry)) {
                    $topLogprobs[] = TopLogprobs::from($entry);
                }
            }
        }

        return new self(
            token: (string) ($attributes['token'] ?? ''),
            logprob: (float) ($attributes['logprob'] ?? 0.0),
            bytes: $bytes,
            topLogprobs: $topLogprobs,
            extras: $extras,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'token' => $this->token,
            'logprob' => $this->logprob,
        ];

        if ($this->bytes !== null) {
            $data['bytes'] = $this->bytes;
        }

        if ($this->topLogprobs !== []) {
            $data['top_logprobs'] = array_map(
                static fn (TopLogprobs $t): array => $t->toArray(),
                $this->topLogprobs,
            );
        }

        foreach ($this->extras as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }
}
