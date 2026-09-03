<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Audio;

/**
 * One timed word, present when `timestamp_granularities` included `word`.
 */
final class TranscriptionWord
{
    /**
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly string $word,
        public readonly float $start,
        public readonly float $end,
        public readonly ?int $speaker,
        public readonly array $extras = [],
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $float = static fn (string $k): float => isset($attributes[$k]) && is_numeric($attributes[$k])
            ? (float) $attributes[$k]
            : 0.0;

        return new self(
            is_string($attributes['word'] ?? null) ? $attributes['word'] : '',
            $float('start'),
            $float('end'),
            is_int($attributes['speaker'] ?? null) ? $attributes['speaker'] : null,
            array_diff_key($attributes, array_flip(['word', 'start', 'end', 'speaker'])),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['word' => $this->word, 'start' => $this->start, 'end' => $this->end];

        if ($this->speaker !== null) {
            $data['speaker'] = $this->speaker;
        }

        return [...$data, ...$this->extras];
    }
}
