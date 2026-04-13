<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Activity;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * @phpstan-import-type ActivityItemType from ActivityItem
 *
 * @phpstan-type ListActivityResponseType array{data: array<int, ActivityItemType>}
 *
 * @implements ResponseContract<ListActivityResponseType>
 */
final class ListActivityResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<ListActivityResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    /**
     * @param  array<int, ActivityItem>  $data
     */
    private function __construct(
        public readonly array $data,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  ListActivityResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $items = array_map(
            static fn (array $item): ActivityItem => ActivityItem::from($item),
            $attributes['data'],
        );

        return new self($items, $meta);
    }

    /**
     * @return ListActivityResponseType
     */
    public function toArray(): array
    {
        return [
            'data' => array_map(
                static fn (ActivityItem $item): array => $item->toArray(),
                $this->data,
            ),
        ];
    }
}
