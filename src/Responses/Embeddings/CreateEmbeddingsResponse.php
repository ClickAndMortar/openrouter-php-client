<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Embeddings;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * Typed wrapper around OpenRouter's embeddings response schema for
 * `POST /embeddings`.
 *
 * @phpstan-import-type EmbeddingDataType from EmbeddingData
 * @phpstan-import-type EmbeddingsUsageType from EmbeddingsUsage
 *
 * @phpstan-type CreateEmbeddingsResponseType array{
 *     object: string,
 *     data: list<EmbeddingDataType>,
 *     model: string,
 *     id?: string,
 *     usage?: EmbeddingsUsageType,
 * }
 *
 * @implements ResponseContract<CreateEmbeddingsResponseType>
 */
final class CreateEmbeddingsResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<CreateEmbeddingsResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    /**
     * @param  list<EmbeddingData>  $data
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly string $object,
        public readonly string $model,
        public readonly array $data,
        public readonly ?string $id,
        public readonly ?EmbeddingsUsage $usage,
        public readonly array $extras,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $rawData = isset($attributes['data']) && is_array($attributes['data'])
            ? $attributes['data']
            : [];

        $data = array_values(array_map(
            static fn (array $item): EmbeddingData => EmbeddingData::from($item),
            array_filter($rawData, 'is_array'),
        ));

        $known = ['id', 'object', 'model', 'data', 'usage'];
        $extras = array_diff_key($attributes, array_flip($known));

        return new self(
            object: is_string($attributes['object'] ?? null) ? $attributes['object'] : 'list',
            model: is_string($attributes['model'] ?? null) ? $attributes['model'] : '',
            data: $data,
            id: isset($attributes['id']) && is_string($attributes['id']) ? $attributes['id'] : null,
            usage: isset($attributes['usage']) && is_array($attributes['usage'])
                ? EmbeddingsUsage::from($attributes['usage'])
                : null,
            extras: $extras,
            meta: $meta,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $out = [
            'object' => $this->object,
            'data' => array_map(static fn (EmbeddingData $d): array => $d->toArray(), $this->data),
            'model' => $this->model,
        ];

        if ($this->id !== null) {
            $out['id'] = $this->id;
        }

        if ($this->usage !== null) {
            $out['usage'] = $this->usage->toArray();
        }

        return [...$out, ...$this->extras];
    }
}
