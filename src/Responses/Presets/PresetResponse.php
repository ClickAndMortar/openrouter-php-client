<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Presets;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * A single preset. Also returned by the preset-scoped inference endpoints,
 * which record the request they were sent as a new version.
 *
 * @phpstan-type PresetResponseType array<string, mixed>
 *
 * @implements ResponseContract<PresetResponseType>
 */
final class PresetResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<PresetResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    private function __construct(
        public readonly Preset $data,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  PresetResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $raw = is_array($attributes['data'] ?? null) ? $attributes['data'] : [];

        return new self(Preset::from($raw),
            $meta,
        );
    }

    /**
     * @return PresetResponseType
     */
    public function toArray(): array
    {
        $data = ['data' => $this->data->toArray()];

        return $data;
    }
}
