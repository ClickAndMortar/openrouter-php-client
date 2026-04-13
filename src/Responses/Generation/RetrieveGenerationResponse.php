<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Generation;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * @phpstan-import-type GenerationDataType from GenerationData
 *
 * @phpstan-type RetrieveGenerationResponseType array{data: GenerationDataType}
 *
 * @implements ResponseContract<RetrieveGenerationResponseType>
 */
final class RetrieveGenerationResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<RetrieveGenerationResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    private function __construct(
        public readonly GenerationData $data,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  RetrieveGenerationResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        return new self(
            data: GenerationData::from($attributes['data']),
            meta: $meta,
        );
    }

    /**
     * @return RetrieveGenerationResponseType
     */
    public function toArray(): array
    {
        return ['data' => $this->data->toArray()];
    }
}
