<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Oauth;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * RFC 7517 JWK Set of the public keys OpenRouter signs access tokens with.
 *
 * @phpstan-type JwksResponseType array<string, mixed>
 *
 * @implements ResponseContract<JwksResponseType>
 */
final class JwksResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<JwksResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    /**
     * @param  list<Jwk>  $keys
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly array $keys,
        public readonly array $extras,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  JwksResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $keys = [];

        if (is_array($attributes['keys'] ?? null)) {
            foreach ($attributes['keys'] as $key) {
                if (is_array($key)) {
                    $keys[] = Jwk::from($key);
                }
            }
        }

        return new self($keys, array_diff_key($attributes, array_flip(['keys'])), $meta);
    }

    /**
     * Looks up a key by its `kid`, which is how a JWT header identifies the key
     * a token was signed with. Returns null when the set does not carry it.
     */
    public function findKey(string $kid): ?Jwk
    {
        foreach ($this->keys as $key) {
            if ($key->kid === $kid) {
                return $key;
            }
        }

        return null;
    }

    /**
     * @return JwksResponseType
     */
    public function toArray(): array
    {
        return [
            'keys' => array_map(static fn (Jwk $key): array => $key->toArray(), $this->keys),
            ...$this->extras,
        ];
    }
}
