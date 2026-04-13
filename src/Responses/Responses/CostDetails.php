<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses;

/**
 * Typed breakdown of the upstream cost detail surface on response usage.
 *
 * @phpstan-type CostDetailsType array{
 *     upstream_inference_cost?: float|null,
 *     upstream_inference_input_cost: float,
 *     upstream_inference_output_cost: float,
 * }
 */
final class CostDetails
{
    /**
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly ?float $upstreamInferenceCost,
        public readonly float $upstreamInferenceInputCost,
        public readonly float $upstreamInferenceOutputCost,
        public readonly array $extras,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $known = [
            'upstream_inference_cost',
            'upstream_inference_input_cost',
            'upstream_inference_output_cost',
        ];
        $extras = array_diff_key($attributes, array_flip($known));

        return new self(
            upstreamInferenceCost: array_key_exists('upstream_inference_cost', $attributes) && $attributes['upstream_inference_cost'] !== null
                ? (float) $attributes['upstream_inference_cost']
                : null,
            upstreamInferenceInputCost: (float) ($attributes['upstream_inference_input_cost'] ?? 0.0),
            upstreamInferenceOutputCost: (float) ($attributes['upstream_inference_output_cost'] ?? 0.0),
            extras: $extras,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'upstream_inference_input_cost' => $this->upstreamInferenceInputCost,
            'upstream_inference_output_cost' => $this->upstreamInferenceOutputCost,
        ];

        if ($this->upstreamInferenceCost !== null) {
            $data['upstream_inference_cost'] = $this->upstreamInferenceCost;
        }

        foreach ($this->extras as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }
}
