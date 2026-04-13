<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Organization;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * Typed wrapper around `GET /organization/members`.
 *
 * @phpstan-import-type OrganizationMemberType from OrganizationMember
 *
 * @phpstan-type ListMembersResponseType array{
 *     data: list<OrganizationMemberType>,
 *     total_count: int,
 * }
 *
 * @implements ResponseContract<ListMembersResponseType>
 */
final class ListMembersResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<ListMembersResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    /**
     * @param  list<OrganizationMember>  $data
     */
    private function __construct(
        public readonly array $data,
        public readonly int $totalCount,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $raw = isset($attributes['data']) && is_array($attributes['data']) ? $attributes['data'] : [];

        $data = array_values(array_map(
            static fn (array $item): OrganizationMember => OrganizationMember::from($item),
            array_filter($raw, 'is_array'),
        ));

        return new self(
            data: $data,
            totalCount: is_int($attributes['total_count'] ?? null) ? $attributes['total_count'] : 0,
            meta: $meta,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'data' => array_map(static fn (OrganizationMember $m): array => $m->toArray(), $this->data),
            'total_count' => $this->totalCount,
        ];
    }
}
