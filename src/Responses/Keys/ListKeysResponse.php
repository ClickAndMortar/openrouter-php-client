<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Keys;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * Typed wrapper around `GET /keys` responses.
 *
 * @phpstan-type ListKeysResponseType array{data: list<array<string, mixed>>}
 *
 * @implements ResponseContract<ListKeysResponseType>
 */
final class ListKeysResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<ListKeysResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    /**
     * @param  list<ApiKeyDetail>  $data
     */
    private function __construct(
        public readonly array $data,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $raw = isset($attributes['data']) && is_array($attributes['data']) ? $attributes['data'] : [];

        $data = array_values(array_map(
            static fn (array $item): ApiKeyDetail => ApiKeyDetail::from($item),
            array_filter($raw, 'is_array'),
        ));

        return new self(data: $data, meta: $meta);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'data' => array_map(static fn (ApiKeyDetail $k): array => $k->toArray(), $this->data),
        ];
    }
}
