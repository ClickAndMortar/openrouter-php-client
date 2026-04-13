<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Models;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * @phpstan-import-type ListEndpointsResponseModelType from ListEndpointsResponseModel
 *
 * @phpstan-type ListEndpointsResponseType array{data: ListEndpointsResponseModelType}
 *
 * @implements ResponseContract<ListEndpointsResponseType>
 */
final class ListEndpointsResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<ListEndpointsResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    private function __construct(
        public readonly ListEndpointsResponseModel $data,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  ListEndpointsResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        return new self(
            data: ListEndpointsResponseModel::from($attributes['data']),
            meta: $meta,
        );
    }

    /**
     * @return ListEndpointsResponseType
     */
    public function toArray(): array
    {
        return ['data' => $this->data->toArray()];
    }
}
