<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Videos;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * An asynchronous video generation job. Poll {@see $pollingUrl} (or call
 * `$client->videos()->retrieve($id)`) until `$status` settles, then fetch the
 * bytes with `$client->videos()->download($id)`.
 *
 * @phpstan-type VideoJobResponseType array<string, mixed>
 *
 * @implements ResponseContract<VideoJobResponseType>
 */
final class VideoJobResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<VideoJobResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    /**
     * @param  list<string>|null  $unsignedUrls
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly string $id,
        public readonly string $pollingUrl,
        public readonly string $status,
        public readonly ?string $generationId,
        public readonly ?string $error,
        public readonly ?array $unsignedUrls,
        public readonly ?VideoUsage $usage,
        public readonly array $extras,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  VideoJobResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $extras = array_diff_key($attributes, array_flip([
            'id', 'polling_url', 'status', 'generation_id', 'error', 'unsigned_urls', 'usage',
        ]));

        /** @var list<string>|null $urls */
        $urls = isset($attributes['unsigned_urls']) && is_array($attributes['unsigned_urls'])
            ? array_values(array_map('strval', $attributes['unsigned_urls']))
            : null;

        return new self(
            id: is_string($attributes['id'] ?? null) ? $attributes['id'] : '',
            pollingUrl: is_string($attributes['polling_url'] ?? null) ? $attributes['polling_url'] : '',
            status: is_string($attributes['status'] ?? null) ? $attributes['status'] : '',
            generationId: is_string($attributes['generation_id'] ?? null) ? $attributes['generation_id'] : null,
            error: is_string($attributes['error'] ?? null) ? $attributes['error'] : null,
            unsignedUrls: $urls,
            usage: isset($attributes['usage']) && is_array($attributes['usage'])
                ? VideoUsage::from($attributes['usage'])
                : null,
            extras: $extras,
            meta: $meta,
        );
    }

    public function isTerminal(): bool
    {
        return in_array($this->status, ['completed', 'succeeded', 'failed', 'cancelled'], true);
    }

    /**
     * @return VideoJobResponseType
     */
    public function toArray(): array
    {
        $data = [
            'id' => $this->id,
            'polling_url' => $this->pollingUrl,
            'status' => $this->status,
        ];

        foreach ([
            'generation_id' => $this->generationId,
            'error' => $this->error,
            'unsigned_urls' => $this->unsignedUrls,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        if ($this->usage instanceof VideoUsage) {
            $data['usage'] = $this->usage->toArray();
        }

        return [...$data, ...$this->extras];
    }
}
