<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Byok;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * Confirmation that a BYOK credential was deleted.
 *
 * @phpstan-type DeleteByokKeyResponseType array<string, mixed>
 *
 * @implements ResponseContract<DeleteByokKeyResponseType>
 */
final class DeleteByokKeyResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<DeleteByokKeyResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    private function __construct(
        public readonly bool $deleted,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  DeleteByokKeyResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        return new self((bool) ($attributes['deleted'] ?? false), $meta);
    }

    /**
     * @return DeleteByokKeyResponseType
     */
    public function toArray(): array
    {
        return ['deleted' => $this->deleted];
    }
}
