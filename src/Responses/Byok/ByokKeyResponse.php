<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Byok;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * A single BYOK provider credential.
 *
 * @phpstan-type ByokKeyResponseType array<string, mixed>
 *
 * @implements ResponseContract<ByokKeyResponseType>
 */
final class ByokKeyResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<ByokKeyResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    private function __construct(
        public readonly ByokKey $data,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  ByokKeyResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $raw = is_array($attributes['data'] ?? null) ? $attributes['data'] : [];

        return new self(ByokKey::from($raw),
            $meta,
        );
    }

    /**
     * @return ByokKeyResponseType
     */
    public function toArray(): array
    {
        $data = ['data' => $this->data->toArray()];

        return $data;
    }
}
