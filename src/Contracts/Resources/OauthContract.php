<?php

declare(strict_types=1);

namespace OpenRouter\Contracts\Resources;

use OpenRouter\Responses\Oauth\JwksResponse;
use OpenRouter\Responses\Oauth\TokenExchangeResponse;
use OpenRouter\ValueObjects\Oauth\TokenExchangeRequest;

interface OauthContract
{
    /**
     * The RFC 7517 JWK Set of public keys OpenRouter signs access tokens with,
     * for verifying a token locally.
     */
    public function jwks(): JwksResponse;

    /**
     * RFC 8693 token exchange: presents a JWT from an issuer your organization
     * trusts and returns a short-lived OpenRouter access token.
     *
     * Authenticates with the subject token, so the client's API key is not
     * sent on this call.
     *
     * @param  TokenExchangeRequest|array<string, mixed>  $request
     */
    public function exchangeToken(TokenExchangeRequest|array $request): TokenExchangeResponse;
}
