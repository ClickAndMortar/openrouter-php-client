<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Auth;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * Typed wrapper around `POST /auth/keys` responses.
 *
 * @phpstan-type ExchangeCodeResponseType array{key: string, user_id: ?string}
 *
 * @implements ResponseContract<ExchangeCodeResponseType>
 */
final class ExchangeCodeResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<ExchangeCodeResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    private function __construct(
        public readonly string $key,
        public readonly ?string $userId,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        return new self(
            key: is_string($attributes['key'] ?? null) ? $attributes['key'] : '',
            userId: isset($attributes['user_id']) && is_string($attributes['user_id']) ? $attributes['user_id'] : null,
            meta: $meta,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'user_id' => $this->userId,
        ];
    }
}
