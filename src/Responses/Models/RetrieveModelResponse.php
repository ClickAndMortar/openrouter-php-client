<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Models;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * A single model looked up by author and slug. Carries the same shape as an
 * entry in the models listing.
 *
 * @phpstan-type RetrieveModelResponseType array<string, mixed>
 *
 * @implements ResponseContract<RetrieveModelResponseType>
 */
final class RetrieveModelResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<RetrieveModelResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    private function __construct(
        public readonly ListResponseModel $data,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  RetrieveModelResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $raw = is_array($attributes['data'] ?? null) ? $attributes['data'] : [];

        /** @phpstan-ignore-next-line — ListResponseModel::from validates the shape at runtime */
        return new self(ListResponseModel::from($raw), $meta);
    }

    /**
     * @return RetrieveModelResponseType
     */
    public function toArray(): array
    {
        return ['data' => $this->data->toArray()];
    }
}
