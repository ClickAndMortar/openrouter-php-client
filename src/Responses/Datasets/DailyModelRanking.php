<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Datasets;

/**
 * A single model's token total for one day.
 */
final class DailyModelRanking
{
    /**
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly ?string $date,
        public readonly ?string $modelPermaslug,
        public readonly ?int $totalTokens,
        public readonly array $extras = [],
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $extras = array_diff_key($attributes, array_flip([
            'date',
            'model_permaslug',
            'total_tokens',
        ]));

        return new self(
            date: is_string($attributes['date'] ?? null) ? $attributes['date'] : null,
            modelPermaslug: is_string($attributes['model_permaslug'] ?? null) ? $attributes['model_permaslug'] : null,
            totalTokens: is_int($attributes['total_tokens'] ?? null) ? $attributes['total_tokens'] : null,
            extras: $extras,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];

        foreach ([
            'date' => $this->date,
            'model_permaslug' => $this->modelPermaslug,
            'total_tokens' => $this->totalTokens,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return [...$data, ...$this->extras];
    }
}
