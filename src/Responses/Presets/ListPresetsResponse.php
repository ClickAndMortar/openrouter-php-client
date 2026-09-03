<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Presets;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * A page of presets.
 *
 * @phpstan-type ListPresetsResponseType array<string, mixed>
 *
 * @implements ResponseContract<ListPresetsResponseType>
 */
final class ListPresetsResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<ListPresetsResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    /**
     * @param  array<int, Preset>  $data
     */
    private function __construct(
        public readonly array $data,
        public readonly ?int $totalCount,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  ListPresetsResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $raw = is_array($attributes['data'] ?? null) ? $attributes['data'] : [];

        return new self(
            array_values(array_map(
                static fn (array $item): Preset => Preset::from($item),
                array_filter($raw, 'is_array'),
            )),
            is_int($attributes['total_count'] ?? null) ? $attributes['total_count'] : null,
            $meta,
        );
    }

    /**
     * @return ListPresetsResponseType
     */
    public function toArray(): array
    {
        $data = ['data' => array_map(static fn (Preset $i): array => $i->toArray(), $this->data)];

        if ($this->totalCount !== null) {
            $data['total_count'] = $this->totalCount;
        }

        return $data;
    }
}
