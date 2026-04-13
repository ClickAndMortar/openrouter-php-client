<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Keys;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * Typed wrapper around `DELETE /keys/{hash}` responses.
 *
 * @phpstan-type DeleteKeyResponseType array{deleted: bool}
 *
 * @implements ResponseContract<DeleteKeyResponseType>
 */
final class DeleteKeyResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<DeleteKeyResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    private function __construct(
        public readonly bool $deleted,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        return new self(
            deleted: (bool) ($attributes['deleted'] ?? false),
            meta: $meta,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return ['deleted' => $this->deleted];
    }
}
