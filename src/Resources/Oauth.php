<?php

declare(strict_types=1);

namespace OpenRouter\Resources;

use OpenRouter\Contracts\Resources\OauthContract;
use OpenRouter\Responses\Oauth\JwksResponse;
use OpenRouter\Responses\Oauth\TokenExchangeResponse;
use OpenRouter\ValueObjects\Oauth\TokenExchangeRequest;
use OpenRouter\ValueObjects\Transporter\Payload;

final class Oauth implements OauthContract
{
    use Concerns\Transportable;

    /**
     * @see https://openrouter.ai/docs/api-reference/openrouter-access-token-signing-keys
     */
    public function jwks(): JwksResponse
    {
        $response = $this->transporter->requestObject(Payload::list('oauth/jwks'));

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return JwksResponse::from($data, $response->meta());
    }

    /**
     * @param  TokenExchangeRequest|array<string, mixed>  $request
     *
     * @see https://openrouter.ai/docs/api-reference/exchange-a-workload-identity-token
     */
    public function exchangeToken(TokenExchangeRequest|array $request): TokenExchangeResponse
    {
        $exchange = $request instanceof TokenExchangeRequest
            ? $request
            : TokenExchangeRequest::from($request);

        // The subject token in the body is the credential here, so the API key
        // is deliberately withheld.
        $payload = Payload::form('oauth/token', $exchange->toArray(), withoutAuthorization: true);

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return TokenExchangeResponse::from($data, $response->meta());
    }
}
