<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Models;

/**
 * Model pricing. OpenRouter returns numeric values as strings (e.g. "0.00003")
 * via the `BigNumberUnion` schema — sometimes as numbers. We preserve them as-is
 * (string|float|int|null) so rounding errors don't creep in. The raw payload is
 * also preserved via `toArray()`.
 *
 * @phpstan-type ListResponseModelPricingType array{
 *     prompt: string|float|int,
 *     completion: string|float|int,
 *     request?: string|float|int|null,
 *     image?: string|float|int|null,
 *     audio?: string|float|int|null,
 *     web_search?: string|float|int|null,
 *     internal_reasoning?: string|float|int|null,
 *     input_cache_read?: string|float|int|null,
 *     input_cache_write?: string|float|int|null,
 *     discount?: float|int|null,
 * }
 */
final class ListResponseModelPricing
{
    /**
     * @param  array<string, string|float|int|null>  $extras
     */
    private function __construct(
        public readonly string|float|int $prompt,
        public readonly string|float|int $completion,
        public readonly string|float|int|null $request,
        public readonly string|float|int|null $image,
        public readonly string|float|int|null $audio,
        public readonly string|float|int|null $webSearch,
        public readonly string|float|int|null $internalReasoning,
        public readonly string|float|int|null $inputCacheRead,
        public readonly string|float|int|null $inputCacheWrite,
        public readonly float|int|null $discount,
        public readonly array $extras,
    ) {
    }

    /**
     * @param  ListResponseModelPricingType  $attributes
     */
    public static function from(array $attributes): self
    {
        $known = [
            'prompt', 'completion', 'request', 'image', 'audio', 'web_search',
            'internal_reasoning', 'input_cache_read', 'input_cache_write', 'discount',
        ];
        /** @var array<string, string|float|int|null> $extras */
        $extras = array_diff_key($attributes, array_flip($known));

        return new self(
            prompt: $attributes['prompt'],
            completion: $attributes['completion'],
            request: $attributes['request'] ?? null,
            image: $attributes['image'] ?? null,
            audio: $attributes['audio'] ?? null,
            webSearch: $attributes['web_search'] ?? null,
            internalReasoning: $attributes['internal_reasoning'] ?? null,
            inputCacheRead: $attributes['input_cache_read'] ?? null,
            inputCacheWrite: $attributes['input_cache_write'] ?? null,
            discount: $attributes['discount'] ?? null,
            extras: $extras,
        );
    }

    /**
     * @return ListResponseModelPricingType
     */
    public function toArray(): array
    {
        $data = [
            'prompt' => $this->prompt,
            'completion' => $this->completion,
        ];

        foreach ([
            'request' => $this->request,
            'image' => $this->image,
            'audio' => $this->audio,
            'web_search' => $this->webSearch,
            'internal_reasoning' => $this->internalReasoning,
            'input_cache_read' => $this->inputCacheRead,
            'input_cache_write' => $this->inputCacheWrite,
            'discount' => $this->discount,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        /** @var ListResponseModelPricingType $data */
        $data = [...$data, ...$this->extras];

        return $data;
    }
}
