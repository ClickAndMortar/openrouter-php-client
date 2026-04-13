<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Chat;

use OpenRouter\Responses\Responses\CostDetails;

/**
 * Token usage statistics for a chat completion. Mirrors `ChatUsage`. Distinct
 * from {@see \OpenRouter\Responses\Responses\CreateResponseUsage} because the
 * `/chat/completions` endpoint uses OpenAI's `prompt_tokens`/`completion_tokens`
 * naming instead of `/responses`'s `input_tokens`/`output_tokens`.
 *
 * `cost` and `cost_details` are OpenRouter-specific extensions — they are not
 * part of the upstream OpenAI shape and may be absent on some backends.
 *
 * @phpstan-type ChatUsageType array{
 *     prompt_tokens: int,
 *     completion_tokens: int,
 *     total_tokens: int,
 *     prompt_tokens_details?: array<string, mixed>|null,
 *     completion_tokens_details?: array<string, mixed>|null,
 *     cost?: float,
 *     cost_details?: array<string, mixed>,
 *     is_byok?: bool,
 * }
 */
final class ChatUsage
{
    /**
     * @param  array<string, mixed>|null  $promptTokensDetails
     * @param  array<string, mixed>|null  $completionTokensDetails
     */
    private function __construct(
        public readonly int $promptTokens,
        public readonly int $completionTokens,
        public readonly int $totalTokens,
        public readonly ?array $promptTokensDetails,
        public readonly ?array $completionTokensDetails,
        public readonly ?float $cost,
        public readonly ?CostDetails $costDetails,
        public readonly ?bool $isByok,
    ) {
    }

    public function cachedTokens(): ?int
    {
        $value = $this->promptTokensDetails['cached_tokens'] ?? null;

        return is_int($value) ? $value : null;
    }

    public function reasoningTokens(): ?int
    {
        $value = $this->completionTokensDetails['reasoning_tokens'] ?? null;

        return is_int($value) ? $value : null;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $costDetails = null;
        if (isset($attributes['cost_details']) && is_array($attributes['cost_details'])) {
            $costDetails = CostDetails::from($attributes['cost_details']);
        }

        return new self(
            promptTokens: is_int($attributes['prompt_tokens'] ?? null) ? $attributes['prompt_tokens'] : 0,
            completionTokens: is_int($attributes['completion_tokens'] ?? null) ? $attributes['completion_tokens'] : 0,
            totalTokens: is_int($attributes['total_tokens'] ?? null) ? $attributes['total_tokens'] : 0,
            promptTokensDetails: isset($attributes['prompt_tokens_details']) && is_array($attributes['prompt_tokens_details'])
                ? $attributes['prompt_tokens_details']
                : null,
            completionTokensDetails: isset($attributes['completion_tokens_details']) && is_array($attributes['completion_tokens_details'])
                ? $attributes['completion_tokens_details']
                : null,
            cost: isset($attributes['cost']) ? (float) $attributes['cost'] : null,
            costDetails: $costDetails,
            isByok: isset($attributes['is_byok']) ? (bool) $attributes['is_byok'] : null,
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

        if ($this->promptTokensDetails !== null) {
            $data['prompt_tokens_details'] = $this->promptTokensDetails;
        }

        if ($this->completionTokensDetails !== null) {
            $data['completion_tokens_details'] = $this->completionTokensDetails;
        }

        if ($this->cost !== null) {
            $data['cost'] = $this->cost;
        }

        if ($this->costDetails !== null) {
            $data['cost_details'] = $this->costDetails->toArray();
        }

        if ($this->isByok !== null) {
            $data['is_byok'] = $this->isByok;
        }

        return $data;
    }
}
