<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Audio;

final class TranscriptionUsage
{
    /**
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly ?float $seconds,
        public readonly ?int $inputTokens,
        public readonly ?int $outputTokens,
        public readonly ?int $totalTokens,
        public readonly ?float $cost,
        public readonly array $extras = [],
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $float = static fn (string $k): ?float => isset($attributes[$k]) && is_numeric($attributes[$k])
            ? (float) $attributes[$k]
            : null;
        $int = static fn (string $k): ?int => is_int($attributes[$k] ?? null) ? $attributes[$k] : null;

        return new self(
            $float('seconds'),
            $int('input_tokens'),
            $int('output_tokens'),
            $int('total_tokens'),
            $float('cost'),
            array_diff_key($attributes, array_flip([
                'seconds', 'input_tokens', 'output_tokens', 'total_tokens', 'cost',
            ])),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];

        foreach ([
            'seconds' => $this->seconds,
            'input_tokens' => $this->inputTokens,
            'output_tokens' => $this->outputTokens,
            'total_tokens' => $this->totalTokens,
            'cost' => $this->cost,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return [...$data, ...$this->extras];
    }
}
