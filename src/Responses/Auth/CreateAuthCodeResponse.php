<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Auth;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * Typed wrapper around `POST /auth/keys/code` responses.
 *
 * @phpstan-import-type AuthCodeDataType from AuthCodeData
 *
 * @phpstan-type CreateAuthCodeResponseType array{data: AuthCodeDataType}
 *
 * @implements ResponseContract<CreateAuthCodeResponseType>
 */
final class CreateAuthCodeResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<CreateAuthCodeResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    private function __construct(
        public readonly AuthCodeData $data,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $data = isset($attributes['data']) && is_array($attributes['data'])
            ? AuthCodeData::from($attributes['data'])
            : AuthCodeData::from([]);

        return new self(data: $data, meta: $meta);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return ['data' => $this->data->toArray()];
    }
}
