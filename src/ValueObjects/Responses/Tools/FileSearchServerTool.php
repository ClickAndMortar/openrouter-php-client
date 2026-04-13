<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Tools;

use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * File-search server tool. `vector_store_ids` is required; `filters` and
 * `ranking_options` are passed through as opaque arrays since the spec
 * defines them as deeply-nested discriminated unions not worth modelling.
 */
final class FileSearchServerTool implements Tool
{
    /**
     * @param  list<string>  $vectorStoreIds
     * @param  array<string, mixed>|null  $filters
     * @param  array<string, mixed>|null  $rankingOptions
     */
    public function __construct(
        public readonly array $vectorStoreIds,
        public readonly ?array $filters = null,
        public readonly ?int $maxNumResults = null,
        public readonly ?array $rankingOptions = null,
    ) {
        if ($this->vectorStoreIds === []) {
            throw new InvalidArgumentException('FileSearchServerTool::$vectorStoreIds must not be empty');
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $ids = is_array($attributes['vector_store_ids'] ?? null) ? $attributes['vector_store_ids'] : [];

        return new self(
            vectorStoreIds: array_values(array_map('strval', $ids)),
            filters: isset($attributes['filters']) && is_array($attributes['filters']) ? $attributes['filters'] : null,
            maxNumResults: isset($attributes['max_num_results']) ? (int) $attributes['max_num_results'] : null,
            rankingOptions: isset($attributes['ranking_options']) && is_array($attributes['ranking_options']) ? $attributes['ranking_options'] : null,
        );
    }

    public function type(): string
    {
        return 'file_search';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'type' => $this->type(),
            'vector_store_ids' => $this->vectorStoreIds,
        ];

        if ($this->filters !== null) {
            $data['filters'] = $this->filters;
        }
        if ($this->maxNumResults !== null) {
            $data['max_num_results'] = $this->maxNumResults;
        }
        if ($this->rankingOptions !== null) {
            $data['ranking_options'] = $this->rankingOptions;
        }

        return $data;
    }
}
