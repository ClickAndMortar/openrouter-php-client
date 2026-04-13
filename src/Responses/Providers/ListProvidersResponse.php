<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Providers;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * @phpstan-import-type ProviderItemType from ProviderItem
 *
 * @phpstan-type ListProvidersResponseType array{data: array<int, ProviderItemType>}
 *
 * @implements ResponseContract<ListProvidersResponseType>
 */
final class ListProvidersResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<ListProvidersResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    /**
     * @param  array<int, ProviderItem>  $data
     */
    private function __construct(
        public readonly array $data,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  ListProvidersResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $items = array_map(
            static fn (array $item): ProviderItem => ProviderItem::from($item),
            $attributes['data'],
        );

        return new self($items, $meta);
    }

    /**
     * @return ListProvidersResponseType
     */
    public function toArray(): array
    {
        return [
            'data' => array_map(
                static fn (ProviderItem $item): array => $item->toArray(),
                $this->data,
            ),
        ];
    }
}
