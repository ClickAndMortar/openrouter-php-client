<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Scim;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * Confirmation that a SCIM group mapping was removed.
 *
 * @phpstan-type DeleteScimGroupMappingResponseType array<string, mixed>
 *
 * @implements ResponseContract<DeleteScimGroupMappingResponseType>
 */
final class DeleteScimGroupMappingResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<DeleteScimGroupMappingResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    private function __construct(
        public readonly bool $deleted,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  DeleteScimGroupMappingResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        return new self((bool) ($attributes['deleted'] ?? false), $meta);
    }

    /**
     * @return DeleteScimGroupMappingResponseType
     */
    public function toArray(): array
    {
        return ['deleted' => $this->deleted];
    }
}
