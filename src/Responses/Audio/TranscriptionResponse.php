<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Audio;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * A transcription. `segments` and `words` are only populated when the request
 * asked for the matching `timestamp_granularities`.
 *
 * @phpstan-type TranscriptionResponseType array<string, mixed>
 *
 * @implements ResponseContract<TranscriptionResponseType>
 */
final class TranscriptionResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<TranscriptionResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    /**
     * @param  array<int, TranscriptionSegment>  $segments
     * @param  array<int, TranscriptionWord>  $words
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly string $text,
        public readonly ?string $task,
        public readonly ?string $language,
        public readonly ?float $duration,
        public readonly array $segments,
        public readonly array $words,
        public readonly ?TranscriptionUsage $usage,
        public readonly array $extras,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  TranscriptionResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $rawSegments = is_array($attributes['segments'] ?? null) ? $attributes['segments'] : [];
        $rawWords = is_array($attributes['words'] ?? null) ? $attributes['words'] : [];

        return new self(
            text: is_string($attributes['text'] ?? null) ? $attributes['text'] : '',
            task: is_string($attributes['task'] ?? null) ? $attributes['task'] : null,
            language: is_string($attributes['language'] ?? null) ? $attributes['language'] : null,
            duration: isset($attributes['duration']) && is_numeric($attributes['duration'])
                ? (float) $attributes['duration']
                : null,
            segments: array_values(array_map(
                static fn (array $s): TranscriptionSegment => TranscriptionSegment::from($s),
                array_filter($rawSegments, 'is_array'),
            )),
            words: array_values(array_map(
                static fn (array $w): TranscriptionWord => TranscriptionWord::from($w),
                array_filter($rawWords, 'is_array'),
            )),
            usage: isset($attributes['usage']) && is_array($attributes['usage'])
                ? TranscriptionUsage::from($attributes['usage'])
                : null,
            extras: array_diff_key($attributes, array_flip([
                'text', 'task', 'language', 'duration', 'segments', 'words', 'usage',
            ])),
            meta: $meta,
        );
    }

    /**
     * @return TranscriptionResponseType
     */
    public function toArray(): array
    {
        $data = ['text' => $this->text];

        foreach (['task' => $this->task, 'language' => $this->language, 'duration' => $this->duration] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        if ($this->segments !== []) {
            $data['segments'] = array_map(static fn (TranscriptionSegment $s): array => $s->toArray(), $this->segments);
        }

        if ($this->words !== []) {
            $data['words'] = array_map(static fn (TranscriptionWord $w): array => $w->toArray(), $this->words);
        }

        if ($this->usage instanceof TranscriptionUsage) {
            $data['usage'] = $this->usage->toArray();
        }

        return [...$data, ...$this->extras];
    }
}
