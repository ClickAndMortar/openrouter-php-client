<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Models;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * @phpstan-type CountResponseType array{data: array{count: int}}
 *
 * @implements ResponseContract<CountResponseType>
 */
final class CountResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<CountResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    private function __construct(
        public readonly int $count,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  CountResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        return new self(
            count: $attributes['data']['count'],
            meta: $meta,
        );
    }

    /**
     * @return CountResponseType
     */
    public function toArray(): array
    {
        return ['data' => ['count' => $this->count]];
    }
}
