<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Resources;

use OpenRouter\Exceptions\Http\BadRequestException;
use OpenRouter\Exceptions\Http\InternalServerErrorException;
use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\Factory;
use OpenRouter\Responses\Oauth\JwksResponse;
use OpenRouter\Responses\Oauth\TokenExchangeResponse;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use OpenRouter\ValueObjects\Oauth\TokenExchangeRequest;
use PHPUnit\Framework\TestCase;

final class OauthTest extends TestCase
{
    private const JWKS = [
        'keys' => [
            [
                'kty' => 'EC',
                'crv' => 'P-256',
                'kid' => 'or-2026-09',
                'x' => 'f83OJ3D2xF1Bg8vub9tLe1gHMzV76e8Tus9uPHvRVEU',
                'y' => 'x_FEzRu9m36HLN_tue659LNpXW6pCyStikYjKIWI5a0',
                'alg' => 'ES256',
                'use' => 'sig',
            ],
        ],
    ];

    private const TOKEN = [
        'access_token' => 'eyJhbGciOiJFUzI1NiJ9.payload.sig',
        'issued_token_type' => 'urn:ietf:params:oauth:token-type:access_token',
        'token_type' => 'Bearer',
        'expires_in' => 900,
        'scope' => 'inference',
    ];

    private function client(RecordingHttpClient $http): \OpenRouter\Client
    {
        return (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();
    }

    public function testJwksReadsTheSigningKeySet(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(self::JWKS);

        $response = $this->client($http)->oauth()->jwks();

        $request = $http->lastRequest();
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/oauth/jwks', (string) $request->getUri());

        $this->assertInstanceOf(JwksResponse::class, $response);
        $this->assertCount(1, $response->keys);
        $this->assertSame('or-2026-09', $response->keys[0]->kid);
        $this->assertSame('ES256', $response->keys[0]->alg);
        $this->assertSame('EC', $response->keys[0]->kty);
    }

    public function testJwksCanFindAKeyById(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(self::JWKS);

        $response = $this->client($http)->oauth()->jwks();

        $this->assertSame('or-2026-09', $response->findKey('or-2026-09')?->kid);
        $this->assertNull($response->findKey('nope'));
    }

    public function testExchangeTokenPostsAFormEncodedBody(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(self::TOKEN);

        $response = $this->client($http)->oauth()->exchangeToken(new TokenExchangeRequest(
            subjectToken: 'idp.jwt.value',
            federationPolicyId: '4b2f7d1e-8c3a-4e5f-9a6b-1c2d3e4f5a6b',
        ));

        $request = $http->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/oauth/token', (string) $request->getUri());
        $this->assertSame('application/x-www-form-urlencoded', $request->getHeaderLine('Content-Type'));

        $body = [];
        parse_str((string) $request->getBody(), $body);
        $this->assertSame('urn:ietf:params:oauth:grant-type:token-exchange', $body['grant_type']);
        $this->assertSame('urn:ietf:params:oauth:token-type:jwt', $body['subject_token_type']);
        $this->assertSame('idp.jwt.value', $body['subject_token']);
        $this->assertSame('4b2f7d1e-8c3a-4e5f-9a6b-1c2d3e4f5a6b', $body['federation_policy_id']);
        // Optional fields are omitted rather than sent empty.
        $this->assertArrayNotHasKey('scope', $body);
        $this->assertArrayNotHasKey('requested_token_type', $body);

        $this->assertInstanceOf(TokenExchangeResponse::class, $response);
        $this->assertSame('eyJhbGciOiJFUzI1NiJ9.payload.sig', $response->accessToken);
        $this->assertSame(900, $response->expiresIn);
        $this->assertSame('Bearer', $response->tokenType);
    }

    public function testExchangeTokenDoesNotSendTheApiKey(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(self::TOKEN);

        $this->client($http)->oauth()->exchangeToken(new TokenExchangeRequest(
            subjectToken: 'idp.jwt.value',
            federationPolicyId: 'policy-1',
        ));

        // The exchange authenticates with the subject token, not the API key.
        $this->assertFalse($http->lastRequest()->hasHeader('Authorization'));
    }

    public function testExchangeTokenWorksOnAClientBuiltWithoutAnApiKey(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(self::TOKEN);

        // Token exchange is the one flow that needs no API key, so the README
        // tells people they can skip it entirely.
        $client = (new Factory())->withHttpClient($http)->make();

        $response = $client->oauth()->exchangeToken(new TokenExchangeRequest(
            subjectToken: 'idp.jwt.value',
            federationPolicyId: 'policy-1',
        ));

        $this->assertFalse($http->lastRequest()->hasHeader('Authorization'));
        $this->assertSame('eyJhbGciOiJFUzI1NiJ9.payload.sig', $response->accessToken);
    }

    public function testExchangeTokenForwardsOptionalScope(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(self::TOKEN);

        $this->client($http)->oauth()->exchangeToken(new TokenExchangeRequest(
            subjectToken: 'idp.jwt.value',
            federationPolicyId: 'policy-1',
            scope: 'inference',
            requestedTokenType: 'urn:ietf:params:oauth:token-type:access_token',
        ));

        $body = [];
        parse_str((string) $http->lastRequest()->getBody(), $body);
        $this->assertSame('inference', $body['scope']);
        $this->assertSame('urn:ietf:params:oauth:token-type:access_token', $body['requested_token_type']);
    }

    public function testExchangeTokenAcceptsAPlainArray(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(self::TOKEN);

        $this->client($http)->oauth()->exchangeToken([
            'subject_token' => 'idp.jwt.value',
            'federation_policy_id' => 'policy-1',
        ]);

        $body = [];
        parse_str((string) $http->lastRequest()->getBody(), $body);
        $this->assertSame('idp.jwt.value', $body['subject_token']);
        $this->assertSame('urn:ietf:params:oauth:grant-type:token-exchange', $body['grant_type']);
    }

    public function testExchangeTokenSurfacesTheRfc6749ErrorDescription(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson([
            'error' => 'invalid_grant',
            'error_description' => 'The subject token was not accepted.',
        ], 400);

        try {
            $this->client($http)->oauth()->exchangeToken(new TokenExchangeRequest(
                subjectToken: 'expired.jwt',
                federationPolicyId: 'policy-1',
            ));
            $this->fail('Expected a BadRequestException.');
        } catch (BadRequestException $e) {
            // OAuth errors carry the actionable text in error_description; the
            // `error` field is only the machine-readable code.
            $this->assertSame('The subject token was not accepted.', $e->getMessage());
            $this->assertSame('invalid_grant', $e->getErrorCode());
            $this->assertSame(400, $e->getStatusCode());
        }
    }

    public function testAnOauthErrorWithoutADescriptionStillReportsTheCode(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['error' => 'server_error'], 500);

        try {
            $this->client($http)->oauth()->jwks();
            $this->fail('Expected an InternalServerErrorException.');
        } catch (InternalServerErrorException $e) {
            $this->assertSame('server_error', $e->getMessage());
        }
    }

    public function testTokenExchangeRequestRejectsAnEmptySubjectToken(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new TokenExchangeRequest(subjectToken: '', federationPolicyId: 'policy-1');
    }

    public function testTokenExchangeRequestRejectsAnEmptyFederationPolicyId(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new TokenExchangeRequest(subjectToken: 'idp.jwt.value', federationPolicyId: '');
    }

    public function testTokenExchangeResponseExposesExpiryAsATimestamp(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(self::TOKEN);

        $before = time();
        $response = $this->client($http)->oauth()->exchangeToken(new TokenExchangeRequest(
            subjectToken: 'idp.jwt.value',
            federationPolicyId: 'policy-1',
        ));

        // expires_in is relative; callers caching the token need an absolute point.
        $this->assertGreaterThanOrEqual($before + 900, $response->expiresAt());
        $this->assertLessThanOrEqual(time() + 900, $response->expiresAt());
    }

    public function testResponsesKeepUnknownFieldsInExtras(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson([...self::TOKEN, 'a_new_field' => 'x']);

        $response = $this->client($http)->oauth()->exchangeToken(new TokenExchangeRequest(
            subjectToken: 'idp.jwt.value',
            federationPolicyId: 'policy-1',
        ));

        $this->assertSame('x', $response->extras['a_new_field']);
        $this->assertSame('x', $response->toArray()['a_new_field']);
    }
}
