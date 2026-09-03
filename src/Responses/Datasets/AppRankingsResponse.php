<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Datasets;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * Top apps by token usage.
 *
 * `$metadata` is the dataset's own `meta` block (coverage window, generation
 * time); `meta()` remains the HTTP response metadata, as on every response.
 *
 * @phpstan-type AppRankingsResponseType array<string, mixed>
 *
 * @implements ResponseContract<AppRankingsResponseType>
 */
final class AppRankingsResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<AppRankingsResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    /**
     * @param  array<int, AppRanking>  $data
     * @param  array<string, mixed>  $metadata
     */
    private function __construct(
        public readonly array $data,
        public readonly array $metadata,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  AppRankingsResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $raw = is_array($attributes['data'] ?? null) ? $attributes['data'] : [];

        return new self(
            array_values(array_map(
                static fn (array $item): AppRanking => AppRanking::from($item),
                array_filter($raw, 'is_array'),
            )),
            is_array($attributes['meta'] ?? null) ? $attributes['meta'] : [],
            $meta,
        );
    }

    /**
     * @return AppRankingsResponseType
     */
    public function toArray(): array
    {
        return [
            'data' => array_map(static fn (AppRanking $i): array => $i->toArray(), $this->data),
            'meta' => $this->metadata,
        ];
    }
}
