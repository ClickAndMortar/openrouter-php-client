<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Benchmarks;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * Published benchmark results.
 *
 * Each entry's shape varies with the benchmark family and the filters applied,
 * and the OpenAPI document leaves the item schema open, so rows are returned
 * as raw arrays rather than a type this SDK would be inventing.
 *
 * @phpstan-type ListBenchmarksResponseType array<string, mixed>
 *
 * @implements ResponseContract<ListBenchmarksResponseType>
 */
final class ListBenchmarksResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<ListBenchmarksResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    /**
     * @param  list<array<string, mixed>>  $data
     * @param  array<string, mixed>  $metadata
     */
    private function __construct(
        public readonly array $data,
        public readonly array $metadata,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  ListBenchmarksResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $raw = is_array($attributes['data'] ?? null) ? $attributes['data'] : [];

        /** @var list<array<string, mixed>> $rows */
        $rows = array_values(array_filter($raw, 'is_array'));

        return new self(
            $rows,
            is_array($attributes['meta'] ?? null) ? $attributes['meta'] : [],
            $meta,
        );
    }

    /**
     * @return ListBenchmarksResponseType
     */
    public function toArray(): array
    {
        return ['data' => $this->data, 'meta' => $this->metadata];
    }
}
