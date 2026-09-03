<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Credits;

/**
 * @phpstan-type CreditsDataType array{total_credits: float|int, total_usage: float|int}
 */
final class CreditsData
{
    private function __construct(
        public readonly float $totalCredits,
        public readonly float $totalUsage,
        /** @var array<string, mixed> */
        public readonly array $extras = [],
    ) {
    }

    /**
     * @param  CreditsDataType  $attributes
     */
    public static function from(array $attributes): self
    {
        $extras = array_diff_key($attributes, array_flip([
            'total_credits',
            'total_usage',
        ]));

        return new self(
            totalCredits: (float) $attributes['total_credits'],
            totalUsage: (float) $attributes['total_usage'],
            extras: $extras,
        );
    }

    /**
     * @return array{total_credits: float, total_usage: float}
     */
    public function toArray(): array
    {
        $data = [
            'total_credits' => $this->totalCredits,
            'total_usage' => $this->totalUsage,
        ];

        return [...$data, ...$this->extras];
    }
}
