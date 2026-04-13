<?php

declare(strict_types=1);

namespace OpenRouter\Contracts\Resources;

use OpenRouter\Responses\Auth\CreateAuthCodeResponse;
use OpenRouter\Responses\Auth\ExchangeCodeResponse;
use OpenRouter\ValueObjects\Auth\CreateAuthCodeRequest;
use OpenRouter\ValueObjects\Auth\ExchangeCodeRequest;

interface AuthContract
{
    /**
     * Exchanges an authorization code from the PKCE flow for a
     * user-controlled API key via `POST /auth/keys`.
     *
     * @param  ExchangeCodeRequest|array<string, mixed>  $parameters
     */
    public function exchangeCode(ExchangeCodeRequest|array $parameters): ExchangeCodeResponse;

    /**
     * Creates an authorization code for the PKCE flow via
     * `POST /auth/keys/code`.
     *
     * @param  CreateAuthCodeRequest|array<string, mixed>  $parameters
     */
    public function createAuthCode(CreateAuthCodeRequest|array $parameters): CreateAuthCodeResponse;
}
