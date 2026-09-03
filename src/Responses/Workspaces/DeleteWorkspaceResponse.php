<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Workspaces;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * Confirmation that a workspace was deleted.
 *
 * @phpstan-type DeleteWorkspaceResponseType array<string, mixed>
 *
 * @implements ResponseContract<DeleteWorkspaceResponseType>
 */
final class DeleteWorkspaceResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<DeleteWorkspaceResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    private function __construct(
        public readonly bool $deleted,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  DeleteWorkspaceResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        return new self((bool) ($attributes['deleted'] ?? false), $meta);
    }

    /**
     * @return DeleteWorkspaceResponseType
     */
    public function toArray(): array
    {
        return ['deleted' => $this->deleted];
    }
}
