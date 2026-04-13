<?php

declare(strict_types=1);

namespace OpenRouter\Resources;

use OpenRouter\Contracts\Resources\AuthContract;
use OpenRouter\Responses\Auth\CreateAuthCodeResponse;
use OpenRouter\Responses\Auth\ExchangeCodeResponse;
use OpenRouter\ValueObjects\Auth\CreateAuthCodeRequest;
use OpenRouter\ValueObjects\Auth\ExchangeCodeRequest;
use OpenRouter\ValueObjects\Transporter\Payload;

final class Auth implements AuthContract
{
    use Concerns\Transportable;

    /**
     * @param  ExchangeCodeRequest|array<string, mixed>  $parameters
     */
    public function exchangeCode(ExchangeCodeRequest|array $parameters): ExchangeCodeResponse
    {
        $params = $parameters instanceof ExchangeCodeRequest
            ? $parameters->toArray()
            : $parameters;

        $payload = Payload::create('auth/keys', $params);

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return ExchangeCodeResponse::from($data, $response->meta());
    }

    /**
     * @param  CreateAuthCodeRequest|array<string, mixed>  $parameters
     */
    public function createAuthCode(CreateAuthCodeRequest|array $parameters): CreateAuthCodeResponse
    {
        $params = $parameters instanceof CreateAuthCodeRequest
            ? $parameters->toArray()
            : $parameters;

        $payload = Payload::create('auth/keys/code', $params);

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return CreateAuthCodeResponse::from($data, $response->meta());
    }
}
