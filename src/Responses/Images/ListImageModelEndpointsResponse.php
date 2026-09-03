<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Images;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * @phpstan-type ListImageModelEndpointsResponseType array<string, mixed>
 *
 * @implements ResponseContract<ListImageModelEndpointsResponseType>
 */
final class ListImageModelEndpointsResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<ListImageModelEndpointsResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    /**
     * @param  array<int, ImageModelEndpoint>  $endpoints
     */
    private function __construct(
        public readonly string $id,
        public readonly array $endpoints,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  ListImageModelEndpointsResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $raw = is_array($attributes['endpoints'] ?? null) ? $attributes['endpoints'] : [];

        return new self(
            is_string($attributes['id'] ?? null) ? $attributes['id'] : '',
            array_values(array_map(
                static fn (array $item): ImageModelEndpoint => ImageModelEndpoint::from($item),
                array_filter($raw, 'is_array'),
            )),
            $meta,
        );
    }

    /**
     * @return ListImageModelEndpointsResponseType
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'endpoints' => array_map(static fn (ImageModelEndpoint $e): array => $e->toArray(), $this->endpoints),
        ];
    }
}
