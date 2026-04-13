<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Keys;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * Typed wrapper around `POST /keys` responses. `$key` holds the full API
 * key string returned exactly once at creation time.
 *
 * @phpstan-type CreateKeyResponseType array{data: array<string, mixed>, key: string}
 *
 * @implements ResponseContract<CreateKeyResponseType>
 */
final class CreateKeyResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<CreateKeyResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    private function __construct(
        public readonly ApiKeyDetail $data,
        public readonly string $key,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $data = isset($attributes['data']) && is_array($attributes['data'])
            ? ApiKeyDetail::from($attributes['data'])
            : ApiKeyDetail::from([]);

        return new self(
            data: $data,
            key: is_string($attributes['key'] ?? null) ? $attributes['key'] : '',
            meta: $meta,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'data' => $this->data->toArray(),
            'key' => $this->key,
        ];
    }
}
