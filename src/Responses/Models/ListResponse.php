<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Models;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * @phpstan-import-type ListResponseModelType from ListResponseModel
 *
 * @phpstan-type ListResponseType array{data: array<int, ListResponseModelType>, total_count?: int, links?: array{next: string|null}}
 *
 * @implements ResponseContract<ListResponseType>
 */
final class ListResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<ListResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    /**
     * @param  array<int, ListResponseModel>  $data
     * @param  int|null  $totalCount  Total models matching the query across all pages.
     * @param  string|null  $nextPage  URL of the next page, or null on the last page.
     */
    private function __construct(
        public readonly array $data,
        public readonly ?int $totalCount,
        public readonly ?string $nextPage,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  ListResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $models = array_map(
            static fn (array $model): ListResponseModel => ListResponseModel::from($model),
            $attributes['data'],
        );

        $links = isset($attributes['links']) && is_array($attributes['links']) ? $attributes['links'] : [];

        return new self(
            $models,
            isset($attributes['total_count']) && is_int($attributes['total_count'])
                ? $attributes['total_count']
                : null,
            isset($links['next']) && is_string($links['next']) ? $links['next'] : null,
            $meta,
        );
    }

    /**
     * @return ListResponseType
     */
    public function toArray(): array
    {
        $data = [
            'data' => array_map(
                static fn (ListResponseModel $model): array => $model->toArray(),
                $this->data,
            ),
        ];

        // `total_count` and `links` are required together upstream; older payloads have neither.
        if ($this->totalCount !== null) {
            $data['total_count'] = $this->totalCount;
            $data['links'] = ['next' => $this->nextPage];
        }

        return $data;
    }
}
