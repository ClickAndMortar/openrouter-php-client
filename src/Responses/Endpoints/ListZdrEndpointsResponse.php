<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Endpoints;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;
use OpenRouter\Responses\Models\ListEndpointsResponseEndpoint;

/**
 * @phpstan-import-type ListEndpointsResponseEndpointType from ListEndpointsResponseEndpoint
 *
 * @phpstan-type ListZdrEndpointsResponseType array{data: array<int, ListEndpointsResponseEndpointType>}
 *
 * @implements ResponseContract<ListZdrEndpointsResponseType>
 */
final class ListZdrEndpointsResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<ListZdrEndpointsResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    /**
     * @param  array<int, ListEndpointsResponseEndpoint>  $data
     */
    private function __construct(
        public readonly array $data,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  ListZdrEndpointsResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $items = array_map(
            static fn (array $item): ListEndpointsResponseEndpoint => ListEndpointsResponseEndpoint::from($item),
            $attributes['data'],
        );

        return new self($items, $meta);
    }

    /**
     * @return ListZdrEndpointsResponseType
     */
    public function toArray(): array
    {
        return [
            'data' => array_map(
                static fn (ListEndpointsResponseEndpoint $item): array => $item->toArray(),
                $this->data,
            ),
        ];
    }
}
