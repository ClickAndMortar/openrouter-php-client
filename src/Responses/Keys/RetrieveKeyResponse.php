<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Keys;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * Typed wrapper around `GET /keys/{hash}` and `PATCH /keys/{hash}`.
 *
 * @phpstan-type RetrieveKeyResponseType array{data: array<string, mixed>}
 *
 * @implements ResponseContract<RetrieveKeyResponseType>
 */
final class RetrieveKeyResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<RetrieveKeyResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    private function __construct(
        public readonly ApiKeyDetail $data,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $data = isset($attributes['data']) && is_array($attributes['data'])
            ? ApiKeyDetail::from($attributes['data'])
            : ApiKeyDetail::from([]);

        return new self(data: $data, meta: $meta);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return ['data' => $this->data->toArray()];
    }
}
