<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Scim;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * A page of SCIM groups.
 *
 * @phpstan-type ListScimGroupsResponseType array<string, mixed>
 *
 * @implements ResponseContract<ListScimGroupsResponseType>
 */
final class ListScimGroupsResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<ListScimGroupsResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    /**
     * @param  array<int, ScimGroup>  $data
     */
    private function __construct(
        public readonly array $data,
        public readonly ?int $totalCount,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  ListScimGroupsResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $raw = is_array($attributes['data'] ?? null) ? $attributes['data'] : [];

        return new self(
            array_values(array_map(
                static fn (array $item): ScimGroup => ScimGroup::from($item),
                array_filter($raw, 'is_array'),
            )),
            is_int($attributes['total_count'] ?? null) ? $attributes['total_count'] : null,
            $meta,
        );
    }

    /**
     * @return ListScimGroupsResponseType
     */
    public function toArray(): array
    {
        $data = ['data' => array_map(static fn (ScimGroup $i): array => $i->toArray(), $this->data)];

        if ($this->totalCount !== null) {
            $data['total_count'] = $this->totalCount;
        }

        return $data;
    }
}
