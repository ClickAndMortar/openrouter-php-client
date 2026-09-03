<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Files;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * A page of stored files. `cursor` is only present on the `openrouter` shape;
 * the OpenAI and Anthropic shapes page with `first_id`/`last_id` instead.
 *
 * @phpstan-type ListFilesResponseType array<string, mixed>
 *
 * @implements ResponseContract<ListFilesResponseType>
 */
final class ListFilesResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<ListFilesResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    /**
     * @param  array<int, StoredFile>  $data
     */
    private function __construct(
        public readonly array $data,
        public readonly string $shape,
        public readonly bool $hasMore,
        public readonly ?string $firstId,
        public readonly ?string $lastId,
        public readonly ?string $cursor,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  ListFilesResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $raw = is_array($attributes['data'] ?? null) ? $attributes['data'] : [];

        $files = array_values(array_map(
            static fn (array $item): StoredFile => StoredFile::from($item),
            array_filter($raw, 'is_array'),
        ));

        return new self(
            $files,
            is_string($attributes['_shape'] ?? null) ? $attributes['_shape'] : 'openrouter',
            (bool) ($attributes['has_more'] ?? false),
            is_string($attributes['first_id'] ?? null) ? $attributes['first_id'] : null,
            is_string($attributes['last_id'] ?? null) ? $attributes['last_id'] : null,
            is_string($attributes['cursor'] ?? null) ? $attributes['cursor'] : null,
            $meta,
        );
    }

    /**
     * @return ListFilesResponseType
     */
    public function toArray(): array
    {
        $data = [
            '_shape' => $this->shape,
            'data' => array_map(static fn (StoredFile $f): array => $f->toArray(), $this->data),
            'has_more' => $this->hasMore,
            'first_id' => $this->firstId,
            'last_id' => $this->lastId,
        ];

        if ($this->cursor !== null) {
            $data['cursor'] = $this->cursor;
        }

        return $data;
    }
}
