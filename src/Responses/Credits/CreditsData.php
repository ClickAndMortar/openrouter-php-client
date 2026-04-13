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
    ) {
    }

    /**
     * @param  CreditsDataType  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            totalCredits: (float) $attributes['total_credits'],
            totalUsage: (float) $attributes['total_usage'],
        );
    }

    /**
     * @return array{total_credits: float, total_usage: float}
     */
    public function toArray(): array
    {
        return [
            'total_credits' => $this->totalCredits,
            'total_usage' => $this->totalUsage,
        ];
    }
}
