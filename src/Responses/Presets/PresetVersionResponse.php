<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Presets;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * A single preset version. `data` is null when the requested version number
 * does not exist, which the API reports with a 200 rather than a 404.
 *
 * @phpstan-type PresetVersionResponseType array<string, mixed>
 *
 * @implements ResponseContract<PresetVersionResponseType>
 */
final class PresetVersionResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<PresetVersionResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    private function __construct(
        public readonly ?PresetVersion $data,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  PresetVersionResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $raw = $attributes['data'] ?? null;

        return new self(is_array($raw) ? PresetVersion::from($raw) : null, $meta);
    }

    /**
     * @return PresetVersionResponseType
     */
    public function toArray(): array
    {
        return ['data' => $this->data?->toArray()];
    }
}
