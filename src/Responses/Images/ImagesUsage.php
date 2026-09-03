<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Images;

final class ImagesUsage
{
    /**
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly int $promptTokens,
        public readonly int $completionTokens,
        public readonly int $totalTokens,
        public readonly ?float $cost,
        public readonly ?bool $isByok,
        public readonly array $extras = [],
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            is_int($attributes['prompt_tokens'] ?? null) ? $attributes['prompt_tokens'] : 0,
            is_int($attributes['completion_tokens'] ?? null) ? $attributes['completion_tokens'] : 0,
            is_int($attributes['total_tokens'] ?? null) ? $attributes['total_tokens'] : 0,
            isset($attributes['cost']) && is_numeric($attributes['cost']) ? (float) $attributes['cost'] : null,
            isset($attributes['is_byok']) ? (bool) $attributes['is_byok'] : null,
            array_diff_key($attributes, array_flip([
                'prompt_tokens', 'completion_tokens', 'total_tokens', 'cost', 'is_byok',
            ])),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'prompt_tokens' => $this->promptTokens,
            'completion_tokens' => $this->completionTokens,
            'total_tokens' => $this->totalTokens,
        ];

        if ($this->cost !== null) {
            $data['cost'] = $this->cost;
        }
        if ($this->isByok !== null) {
            $data['is_byok'] = $this->isByok;
        }

        return [...$data, ...$this->extras];
    }
}
