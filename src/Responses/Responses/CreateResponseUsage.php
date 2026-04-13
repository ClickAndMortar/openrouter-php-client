<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses;

/**
 * @phpstan-type CreateResponseUsageType array{
 *     input_tokens: int,
 *     input_tokens_details: array{cached_tokens: int},
 *     output_tokens: int,
 *     output_tokens_details: array{reasoning_tokens: int},
 *     total_tokens: int,
 *     cost?: float,
 *     cost_details?: array{
 *         upstream_inference_cost?: float|null,
 *         upstream_inference_input_cost: float,
 *         upstream_inference_output_cost: float,
 *     },
 *     is_byok?: bool,
 * }
 */
final class CreateResponseUsage
{
    private function __construct(
        public readonly int $inputTokens,
        public readonly int $cachedTokens,
        public readonly int $outputTokens,
        public readonly int $reasoningTokens,
        public readonly int $totalTokens,
        public readonly ?float $cost,
        public readonly ?CostDetails $costDetails,
        public readonly ?bool $isByok,
    ) {
    }

    /**
     * @param  CreateResponseUsageType  $attributes
     */
    public static function from(array $attributes): self
    {
        $costDetails = null;
        if (isset($attributes['cost_details']) && is_array($attributes['cost_details'])) {
            $costDetails = CostDetails::from($attributes['cost_details']);
        }

        return new self(
            inputTokens: $attributes['input_tokens'],
            cachedTokens: $attributes['input_tokens_details']['cached_tokens'],
            outputTokens: $attributes['output_tokens'],
            reasoningTokens: $attributes['output_tokens_details']['reasoning_tokens'],
            totalTokens: $attributes['total_tokens'],
            cost: $attributes['cost'] ?? null,
            costDetails: $costDetails,
            isByok: $attributes['is_byok'] ?? null,
        );
    }

    /**
     * @return CreateResponseUsageType
     */
    public function toArray(): array
    {
        $data = [
            'input_tokens' => $this->inputTokens,
            'input_tokens_details' => ['cached_tokens' => $this->cachedTokens],
            'output_tokens' => $this->outputTokens,
            'output_tokens_details' => ['reasoning_tokens' => $this->reasoningTokens],
            'total_tokens' => $this->totalTokens,
        ];

        if ($this->cost !== null) {
            $data['cost'] = $this->cost;
        }

        if ($this->costDetails !== null) {
            $data['cost_details'] = $this->costDetails->toArray();
        }

        if ($this->isByok !== null) {
            $data['is_byok'] = $this->isByok;
        }

        /** @var CreateResponseUsageType $data */
        return $data;
    }
}
