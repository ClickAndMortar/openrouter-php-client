<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Observability;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * Confirmation that an observability destination was removed.
 *
 * @phpstan-type DeleteObservabilityDestinationResponseType array<string, mixed>
 *
 * @implements ResponseContract<DeleteObservabilityDestinationResponseType>
 */
final class DeleteObservabilityDestinationResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<DeleteObservabilityDestinationResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    private function __construct(
        public readonly bool $deleted,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  DeleteObservabilityDestinationResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        return new self((bool) ($attributes['deleted'] ?? false), $meta);
    }

    /**
     * @return DeleteObservabilityDestinationResponseType
     */
    public function toArray(): array
    {
        return ['deleted' => $this->deleted];
    }
}
