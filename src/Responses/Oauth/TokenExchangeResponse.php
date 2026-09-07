<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Oauth;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * RFC 8693 token exchange result.
 *
 * `$accessToken` is short-lived — at most 15 minutes, and never outliving the
 * subject token it was exchanged for. Send it as `Authorization: Bearer` to
 * the inference API.
 *
 * @phpstan-type TokenExchangeResponseType array<string, mixed>
 *
 * @implements ResponseContract<TokenExchangeResponseType>
 */
final class TokenExchangeResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<TokenExchangeResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    /**
     * @param  int  $expiresIn  Seconds until the access token expires.
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly string $accessToken,
        public readonly string $tokenType,
        public readonly string $issuedTokenType,
        public readonly int $expiresIn,
        public readonly string $scope,
        public readonly array $extras,
        private readonly MetaInformation $meta,
        private readonly int $receivedAt,
    ) {
    }

    /**
     * @param  TokenExchangeResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $string = static fn (string $key): string => is_string($attributes[$key] ?? null)
            ? $attributes[$key]
            : '';

        return new self(
            accessToken: $string('access_token'),
            tokenType: $string('token_type'),
            issuedTokenType: $string('issued_token_type'),
            expiresIn: is_int($attributes['expires_in'] ?? null) ? $attributes['expires_in'] : 0,
            scope: $string('scope'),
            extras: array_diff_key($attributes, array_flip([
                'access_token',
                'token_type',
                'issued_token_type',
                'expires_in',
                'scope',
            ])),
            meta: $meta,
            receivedAt: time(),
        );
    }

    /**
     * Absolute Unix time the token expires, derived from the relative
     * `expires_in` at the moment the response was parsed. Useful for caching,
     * where a relative lifetime alone is not enough.
     */
    public function expiresAt(): int
    {
        return $this->receivedAt + $this->expiresIn;
    }

    /**
     * Whether the token has expired, optionally treating it as expired
     * `$leewaySeconds` early so a caller can refresh before it lapses.
     */
    public function isExpired(int $leewaySeconds = 0): bool
    {
        return time() >= ($this->expiresAt() - $leewaySeconds);
    }

    /**
     * @return TokenExchangeResponseType
     */
    public function toArray(): array
    {
        return [
            'access_token' => $this->accessToken,
            'token_type' => $this->tokenType,
            'issued_token_type' => $this->issuedTokenType,
            'expires_in' => $this->expiresIn,
            'scope' => $this->scope,
            ...$this->extras,
        ];
    }
}
