<?php

declare(strict_types=1);

namespace OpenRouter\Responses;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * A `{ "data": ... }` envelope whose payload the OpenAPI document leaves open.
 *
 * Used by the reporting and diagnostic endpoints — analytics metadata and
 * query results, task classification, stored generation content, feedback
 * acknowledgements — where the shape is driven by the request and typing it
 * would be inventing a contract the spec does not make.
 *
 * @phpstan-type DataResponseType array<string, mixed>
 *
 * @implements ResponseContract<DataResponseType>
 */
final class DataResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<DataResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    /**
     * @param  array<string, mixed>  $data
     */
    private function __construct(
        public readonly array $data,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  DataResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        return new self(
            is_array($attributes['data'] ?? null) ? $attributes['data'] : [],
            $meta,
        );
    }

    /**
     * @return DataResponseType
     */
    public function toArray(): array
    {
        return ['data' => $this->data];
    }
}
