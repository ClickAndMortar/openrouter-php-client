<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Videos;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * @phpstan-type ListVideoModelsResponseType array{data: array<int, array<string, mixed>>}
 *
 * @implements ResponseContract<ListVideoModelsResponseType>
 */
final class ListVideoModelsResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<ListVideoModelsResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    /**
     * @param  array<int, VideoModel>  $data
     */
    private function __construct(
        public readonly array $data,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  ListVideoModelsResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $raw = is_array($attributes['data'] ?? null) ? $attributes['data'] : [];

        $models = array_values(array_map(
            static fn (array $item): VideoModel => VideoModel::from($item),
            array_filter($raw, 'is_array'),
        ));

        return new self($models, $meta);
    }

    /**
     * @return ListVideoModelsResponseType
     */
    public function toArray(): array
    {
        return [
            'data' => array_map(static fn (VideoModel $m): array => $m->toArray(), $this->data),
        ];
    }
}
