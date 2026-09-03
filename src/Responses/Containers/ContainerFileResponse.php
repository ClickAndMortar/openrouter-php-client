<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Containers;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * @phpstan-type ContainerFileResponseType array<string, mixed>
 *
 * @implements ResponseContract<ContainerFileResponseType>
 */
final class ContainerFileResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<ContainerFileResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    private function __construct(
        public readonly ContainerFile $data,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  ContainerFileResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        return new self(ContainerFile::from($attributes), $meta);
    }

    /**
     * @return ContainerFileResponseType
     */
    public function toArray(): array
    {
        return $this->data->toArray();
    }
}
