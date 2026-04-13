<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Credits;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * @phpstan-import-type CreditsDataType from CreditsData
 *
 * @phpstan-type RetrieveCreditsResponseType array{data: CreditsDataType}
 *
 * @implements ResponseContract<RetrieveCreditsResponseType>
 */
final class RetrieveCreditsResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<RetrieveCreditsResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    private function __construct(
        public readonly CreditsData $data,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  RetrieveCreditsResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        return new self(
            data: CreditsData::from($attributes['data']),
            meta: $meta,
        );
    }

    /**
     * @return array{data: array{total_credits: float, total_usage: float}}
     */
    public function toArray(): array
    {
        return ['data' => $this->data->toArray()];
    }
}
