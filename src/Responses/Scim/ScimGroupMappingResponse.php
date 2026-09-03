<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Scim;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * A single SCIM group mapping.
 *
 * @phpstan-type ScimGroupMappingResponseType array<string, mixed>
 *
 * @implements ResponseContract<ScimGroupMappingResponseType>
 */
final class ScimGroupMappingResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<ScimGroupMappingResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    private function __construct(
        public readonly ScimGroupMapping $data,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  ScimGroupMappingResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $raw = is_array($attributes['data'] ?? null) ? $attributes['data'] : [];

        return new self(ScimGroupMapping::from($raw),
            $meta,
        );
    }

    /**
     * @return ScimGroupMappingResponseType
     */
    public function toArray(): array
    {
        $data = ['data' => $this->data->toArray()];

        return $data;
    }
}
