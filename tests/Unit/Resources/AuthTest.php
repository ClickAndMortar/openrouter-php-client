<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Resources;

use OpenRouter\Enums\Auth\CodeChallengeMethod;
use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\Factory;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use OpenRouter\Tests\Fixtures\AuthCreateCodeFixture;
use OpenRouter\Tests\Fixtures\AuthExchangeFixture;
use OpenRouter\ValueObjects\Auth\CreateAuthCodeRequest;
use OpenRouter\ValueObjects\Auth\ExchangeCodeRequest;
use PHPUnit\Framework\TestCase;

final class AuthTest extends TestCase
{
    private function makeClient(RecordingHttpClient $http): \OpenRouter\Client
    {
        return (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();
    }

    public function testExchangeCodeHitsAuthKeysEndpointAsPost(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(AuthExchangeFixture::ATTRIBUTES);

        $response = $this->makeClient($http)->auth()->exchangeCode([
            'code' => 'auth_code_abc123def456',
            'code_verifier' => 'dBjftJeZ4CVP-mB92K27uhbUJU1p1r_wW1gFWFOEjXk',
            'code_challenge_method' => 'S256',
        ]);

        $request = $http->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/auth/keys', (string) $request->getUri());

        $body = json_decode((string) $request->getBody(), true);
        $this->assertSame('auth_code_abc123def456', $body['code']);
        $this->assertSame('dBjftJeZ4CVP-mB92K27uhbUJU1p1r_wW1gFWFOEjXk', $body['code_verifier']);
        $this->assertSame('S256', $body['code_challenge_method']);

        $this->assertSame('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', $response->key);
        $this->assertSame('user_2yOPcMpKoQhcd4bVgSMlELRaIah', $response->userId);
    }

    public function testExchangeCodeSerializesTypedRequestWithEnum(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(AuthExchangeFixture::ATTRIBUTES);

        $request = new ExchangeCodeRequest(
            code: 'auth_code_abc123',
            codeVerifier: 'verifier-value',
            codeChallengeMethod: CodeChallengeMethod::S256,
        );

        $this->makeClient($http)->auth()->exchangeCode($request);

        $body = json_decode((string) $http->lastRequest()->getBody(), true);
        $this->assertSame('auth_code_abc123', $body['code']);
        $this->assertSame('S256', $body['code_challenge_method']);
    }

    public function testCreateAuthCodeHitsAuthKeysCodeEndpointAsPost(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(AuthCreateCodeFixture::ATTRIBUTES);

        $request = new CreateAuthCodeRequest(
            callbackUrl: 'https://myapp.com/auth/callback',
            codeChallenge: 'E9Melhoa2OwvFrEMTJguCHaoeK1t8URWbuGJSstw-cM',
            codeChallengeMethod: CodeChallengeMethod::S256,
            limit: 100.0,
            keyLabel: 'My Custom Key',
        );

        $response = $this->makeClient($http)->auth()->createAuthCode($request);

        $httpRequest = $http->lastRequest();
        $this->assertSame('POST', $httpRequest->getMethod());
        $this->assertStringEndsWith('/auth/keys/code', (string) $httpRequest->getUri());

        $body = json_decode((string) $httpRequest->getBody(), true);
        $this->assertSame('https://myapp.com/auth/callback', $body['callback_url']);
        $this->assertSame('E9Melhoa2OwvFrEMTJguCHaoeK1t8URWbuGJSstw-cM', $body['code_challenge']);
        $this->assertSame('S256', $body['code_challenge_method']);
        $this->assertSame(100, $body['limit']);
        $this->assertSame('My Custom Key', $body['key_label']);

        $this->assertSame('auth_code_xyz789', $response->data->id);
        $this->assertSame(12345, $response->data->appId);
        $this->assertSame('2025-08-24T10:30:00Z', $response->data->createdAt);
    }

    public function testExchangeCodeRejectsEmptyCode(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ExchangeCodeRequest(code: '');
    }

    public function testCreateAuthCodeRejectsEmptyCallback(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CreateAuthCodeRequest(
            callbackUrl: '',
            codeChallenge: 'c',
            codeChallengeMethod: CodeChallengeMethod::S256,
        );
    }

    public function testCreateAuthCodeRejectsLongKeyLabel(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CreateAuthCodeRequest(
            callbackUrl: 'https://a.example',
            codeChallenge: 'c',
            codeChallengeMethod: CodeChallengeMethod::S256,
            keyLabel: str_repeat('a', 101),
        );
    }
}
