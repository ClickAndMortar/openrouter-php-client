<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Images;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * @phpstan-type ImagesResultType array<string, mixed>
 *
 * @implements ResponseContract<ImagesResultType>
 */
final class ImagesResult implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<ImagesResultType> */
    use ArrayAccessible;
    use HasMetaInformation;

    /**
     * @param  array<int, GeneratedImage>  $data
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly int $created,
        public readonly array $data,
        public readonly ?ImagesUsage $usage,
        public readonly array $extras,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  ImagesResultType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $raw = is_array($attributes['data'] ?? null) ? $attributes['data'] : [];

        $images = array_values(array_map(
            static fn (array $item): GeneratedImage => GeneratedImage::from($item),
            array_filter($raw, 'is_array'),
        ));

        return new self(
            is_int($attributes['created'] ?? null) ? $attributes['created'] : 0,
            $images,
            isset($attributes['usage']) && is_array($attributes['usage'])
                ? ImagesUsage::from($attributes['usage'])
                : null,
            array_diff_key($attributes, array_flip(['created', 'data', 'usage'])),
            $meta,
        );
    }

    /**
     * @return ImagesResultType
     */
    public function toArray(): array
    {
        $data = [
            'created' => $this->created,
            'data' => array_map(static fn (GeneratedImage $i): array => $i->toArray(), $this->data),
        ];

        if ($this->usage instanceof ImagesUsage) {
            $data['usage'] = $this->usage->toArray();
        }

        return [...$data, ...$this->extras];
    }
}
