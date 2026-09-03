<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Images;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * @phpstan-type ListImageModelsResponseType array{data: array<int, array<string, mixed>>}
 *
 * @implements ResponseContract<ListImageModelsResponseType>
 */
final class ListImageModelsResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<ListImageModelsResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    /**
     * @param  array<int, ImageModel>  $data
     */
    private function __construct(
        public readonly array $data,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  ListImageModelsResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $raw = is_array($attributes['data'] ?? null) ? $attributes['data'] : [];

        return new self(
            array_values(array_map(
                static fn (array $item): ImageModel => ImageModel::from($item),
                array_filter($raw, 'is_array'),
            )),
            $meta,
        );
    }

    /**
     * @return ListImageModelsResponseType
     */
    public function toArray(): array
    {
        return ['data' => array_map(static fn (ImageModel $m): array => $m->toArray(), $this->data)];
    }
}
