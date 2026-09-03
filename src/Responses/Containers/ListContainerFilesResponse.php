<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Containers;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * A cursor-paginated page of container files.
 *
 * @phpstan-type ListContainerFilesResponseType array<string, mixed>
 *
 * @implements ResponseContract<ListContainerFilesResponseType>
 */
final class ListContainerFilesResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<ListContainerFilesResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    /**
     * @param  array<int, ContainerFile>  $data
     */
    private function __construct(
        public readonly array $data,
        public readonly string $object,
        public readonly bool $hasMore,
        public readonly ?string $firstId,
        public readonly ?string $lastId,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  ListContainerFilesResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $raw = is_array($attributes['data'] ?? null) ? $attributes['data'] : [];

        $files = array_values(array_map(
            static fn (array $item): ContainerFile => ContainerFile::from($item),
            array_filter($raw, 'is_array'),
        ));

        return new self(
            $files,
            is_string($attributes['object'] ?? null) ? $attributes['object'] : 'list',
            (bool) ($attributes['has_more'] ?? false),
            is_string($attributes['first_id'] ?? null) ? $attributes['first_id'] : null,
            is_string($attributes['last_id'] ?? null) ? $attributes['last_id'] : null,
            $meta,
        );
    }

    /**
     * @return ListContainerFilesResponseType
     */
    public function toArray(): array
    {
        return [
            'object' => $this->object,
            'data' => array_map(static fn (ContainerFile $f): array => $f->toArray(), $this->data),
            'first_id' => $this->firstId,
            'last_id' => $this->lastId,
            'has_more' => $this->hasMore,
        ];
    }
}
