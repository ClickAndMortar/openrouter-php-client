<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Audio;

/**
 * One timed segment of a transcription.
 */
final class TranscriptionSegment
{
    /**
     * @param  list<int>|null  $tokens
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly int $id,
        public readonly float $start,
        public readonly float $end,
        public readonly string $text,
        public readonly ?int $seek,
        public readonly ?float $temperature,
        public readonly ?float $avgLogprob,
        public readonly ?float $compressionRatio,
        public readonly ?float $noSpeechProb,
        public readonly ?int $speaker,
        public readonly ?array $tokens,
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

        /** @var list<int>|null $tokens */
        $tokens = is_array($attributes['tokens'] ?? null)
            ? array_values(array_map('intval', $attributes['tokens']))
            : null;

        return new self(
            id: is_int($attributes['id'] ?? null) ? $attributes['id'] : 0,
            start: $float('start') ?? 0.0,
            end: $float('end') ?? 0.0,
            text: is_string($attributes['text'] ?? null) ? $attributes['text'] : '',
            seek: is_int($attributes['seek'] ?? null) ? $attributes['seek'] : null,
            temperature: $float('temperature'),
            avgLogprob: $float('avg_logprob'),
            compressionRatio: $float('compression_ratio'),
            noSpeechProb: $float('no_speech_prob'),
            speaker: is_int($attributes['speaker'] ?? null) ? $attributes['speaker'] : null,
            tokens: $tokens,
            extras: array_diff_key($attributes, array_flip([
                'id', 'start', 'end', 'text', 'seek', 'temperature',
                'avg_logprob', 'compression_ratio', 'no_speech_prob', 'speaker', 'tokens',
            ])),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['id' => $this->id, 'start' => $this->start, 'end' => $this->end, 'text' => $this->text];

        foreach ([
            'seek' => $this->seek,
            'temperature' => $this->temperature,
            'avg_logprob' => $this->avgLogprob,
            'compression_ratio' => $this->compressionRatio,
            'no_speech_prob' => $this->noSpeechProb,
            'speaker' => $this->speaker,
            'tokens' => $this->tokens,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return [...$data, ...$this->extras];
    }
}
