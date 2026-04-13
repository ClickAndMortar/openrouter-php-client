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
 * @phpstan-type ListResponseType array{data: array<int, ListResponseModelType>}
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
     */
    private function __construct(
        public readonly array $data,
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

        return new self($models, $meta);
    }

    /**
     * @return ListResponseType
     */
    public function toArray(): array
    {
        return [
            'data' => array_map(
                static fn (ListResponseModel $model): array => $model->toArray(),
                $this->data,
            ),
        ];
    }
}
