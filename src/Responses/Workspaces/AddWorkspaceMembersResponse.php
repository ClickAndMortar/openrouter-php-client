<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Workspaces;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * The members added by a bulk add, and how many were new.
 *
 * @phpstan-type AddWorkspaceMembersResponseType array<string, mixed>
 *
 * @implements ResponseContract<AddWorkspaceMembersResponseType>
 */
final class AddWorkspaceMembersResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<AddWorkspaceMembersResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    /**
     * @param  array<int, WorkspaceMember>  $data
     */
    private function __construct(
        public readonly array $data,
        public readonly ?int $totalCount,
        public readonly int $addedCount,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  AddWorkspaceMembersResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $raw = is_array($attributes['data'] ?? null) ? $attributes['data'] : [];

        return new self(
            array_values(array_map(
                static fn (array $item): WorkspaceMember => WorkspaceMember::from($item),
                array_filter($raw, 'is_array'),
            )),
            is_int($attributes['total_count'] ?? null) ? $attributes['total_count'] : null,
            is_int($attributes['added_count'] ?? null) ? $attributes['added_count'] : 0,
            $meta,
        );
    }

    /**
     * @return AddWorkspaceMembersResponseType
     */
    public function toArray(): array
    {
        $data = ['data' => array_map(static fn (WorkspaceMember $i): array => $i->toArray(), $this->data)];

        if ($this->totalCount !== null) {
            $data['total_count'] = $this->totalCount;
        }
        $data['added_count'] = $this->addedCount;

        return $data;
    }
}
