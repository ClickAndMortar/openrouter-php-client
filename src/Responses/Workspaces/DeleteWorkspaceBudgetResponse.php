<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Workspaces;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * Confirmation that a workspace budget was removed.
 *
 * @phpstan-type DeleteWorkspaceBudgetResponseType array<string, mixed>
 *
 * @implements ResponseContract<DeleteWorkspaceBudgetResponseType>
 */
final class DeleteWorkspaceBudgetResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<DeleteWorkspaceBudgetResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    private function __construct(
        public readonly bool $deleted,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  DeleteWorkspaceBudgetResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        return new self((bool) ($attributes['deleted'] ?? false), $meta);
    }

    /**
     * @return DeleteWorkspaceBudgetResponseType
     */
    public function toArray(): array
    {
        return ['deleted' => $this->deleted];
    }
}
