<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Oauth;

use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * RFC 8693 token exchange request.
 *
 * `grant_type` and `subject_token_type` are fixed by the spec, so they default
 * to the only accepted values and rarely need passing.
 *
 * @see https://openrouter.ai/docs/api-reference/exchange-a-workload-identity-token
 */
final class TokenExchangeRequest
{
    public const GRANT_TYPE = 'urn:ietf:params:oauth:grant-type:token-exchange';

    public const SUBJECT_TOKEN_TYPE = 'urn:ietf:params:oauth:token-type:jwt';

    public const ACCESS_TOKEN_TYPE = 'urn:ietf:params:oauth:token-type:access_token';

    /**
     * @param  string  $subjectToken  The JWT issued by your identity provider.
     * @param  string  $federationPolicyId  The federation policy to evaluate,
     *                                      from Settings → Workload identity.
     */
    public function __construct(
        public readonly string $subjectToken,
        public readonly string $federationPolicyId,
        public readonly ?string $scope = null,
        public readonly ?string $requestedTokenType = null,
        public readonly string $grantType = self::GRANT_TYPE,
        public readonly string $subjectTokenType = self::SUBJECT_TOKEN_TYPE,
    ) {
        if (trim($subjectToken) === '') {
            throw new InvalidArgumentException('TokenExchangeRequest::$subjectToken must not be empty');
        }

        if (trim($federationPolicyId) === '') {
            throw new InvalidArgumentException('TokenExchangeRequest::$federationPolicyId must not be empty');
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            subjectToken: is_string($attributes['subject_token'] ?? null) ? $attributes['subject_token'] : '',
            federationPolicyId: is_string($attributes['federation_policy_id'] ?? null)
                ? $attributes['federation_policy_id']
                : '',
            scope: is_string($attributes['scope'] ?? null) ? $attributes['scope'] : null,
            requestedTokenType: is_string($attributes['requested_token_type'] ?? null)
                ? $attributes['requested_token_type']
                : null,
            grantType: is_string($attributes['grant_type'] ?? null) ? $attributes['grant_type'] : self::GRANT_TYPE,
            subjectTokenType: is_string($attributes['subject_token_type'] ?? null)
                ? $attributes['subject_token_type']
                : self::SUBJECT_TOKEN_TYPE,
        );
    }

    /**
     * @return array<string, string|null>
     */
    public function toArray(): array
    {
        return [
            'grant_type' => $this->grantType,
            'subject_token' => $this->subjectToken,
            'subject_token_type' => $this->subjectTokenType,
            'federation_policy_id' => $this->federationPolicyId,
            'scope' => $this->scope,
            'requested_token_type' => $this->requestedTokenType,
        ];
    }
}
