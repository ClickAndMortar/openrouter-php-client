<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses;

/**
 * Alternative token with its log probability (nested inside {@see LogProbs}).
 *
 * @phpstan-type TopLogprobsType array{
 *     token: string,
 *     logprob: float,
 *     bytes?: list<int>|null,
 * }
 */
final class TopLogprobs
{
    /**
     * @param  list<int>|null  $bytes
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly string $token,
        public readonly float $logprob,
        public readonly ?array $bytes,
        public readonly array $extras,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $known = ['token', 'logprob', 'bytes'];
        $extras = array_diff_key($attributes, array_flip($known));

        $bytes = null;
        if (isset($attributes['bytes']) && is_array($attributes['bytes'])) {
            $bytes = array_values(array_map('intval', $attributes['bytes']));
        }

        return new self(
            token: (string) ($attributes['token'] ?? ''),
            logprob: (float) ($attributes['logprob'] ?? 0.0),
            bytes: $bytes,
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

        foreach ($this->extras as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }
}
