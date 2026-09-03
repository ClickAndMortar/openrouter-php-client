<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Workspaces;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * How many memberships a bulk remove actually deleted. Unlike the add
 * counterpart, no member records come back — they no longer exist.
 *
 * @phpstan-type RemoveWorkspaceMembersResponseType array<string, mixed>
 *
 * @implements ResponseContract<RemoveWorkspaceMembersResponseType>
 */
final class RemoveWorkspaceMembersResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<RemoveWorkspaceMembersResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    private function __construct(
        public readonly int $removedCount,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  RemoveWorkspaceMembersResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        return new self(
            is_int($attributes['removed_count'] ?? null) ? $attributes['removed_count'] : 0,
            $meta,
        );
    }

    /**
     * @return RemoveWorkspaceMembersResponseType
     */
    public function toArray(): array
    {
        return ['removed_count' => $this->removedCount];
    }
}
