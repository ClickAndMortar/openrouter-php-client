<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Resources;

use OpenRouter\Enums\Keys\LimitReset;
use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\Factory;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use OpenRouter\Tests\Fixtures\KeysCreateFixture;
use OpenRouter\Tests\Fixtures\KeysCurrentFixture;
use OpenRouter\Tests\Fixtures\KeysDeleteFixture;
use OpenRouter\Tests\Fixtures\KeysListFixture;
use OpenRouter\Tests\Fixtures\KeysRetrieveFixture;
use OpenRouter\ValueObjects\Keys\CreateKeyRequest;
use OpenRouter\ValueObjects\Keys\UpdateKeyRequest;
use PHPUnit\Framework\TestCase;

final class KeysTest extends TestCase
{
    private function makeClient(RecordingHttpClient $http): \OpenRouter\Client
    {
        return (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();
    }

    public function testCurrentHitsKeyEndpointAsGet(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(KeysCurrentFixture::ATTRIBUTES);

        $response = $this->makeClient($http)->keys()->current();

        $request = $http->lastRequest();
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/key', (string) $request->getUri());
        $this->assertSame('', (string) $request->getBody());

        $this->assertSame('sk-or-v1-au7...890', $response->data->label);
        $this->assertSame(100.0, $response->data->limit);
        $this->assertSame('monthly', $response->data->limitReset);
        $this->assertFalse($response->data->isManagementKey);
        $this->assertNotNull($response->data->rateLimit);
        $this->assertSame(1000, $response->data->rateLimit['requests']);
    }

    public function testListHitsKeysEndpointAsGetWithQuery(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(KeysListFixture::ATTRIBUTES);

        $response = $this->makeClient($http)->keys()->list(includeDisabled: true, offset: 10);

        $request = $http->lastRequest();
        $this->assertSame('GET', $request->getMethod());
        $uri = (string) $request->getUri();
        $this->assertStringContainsString('/keys?', $uri);
        $this->assertStringContainsString('include_disabled=true', $uri);
        $this->assertStringContainsString('offset=10', $uri);

        $this->assertCount(1, $response->data);
        $this->assertSame('My Production Key', $response->data[0]->name);
        $this->assertSame('f01d52606dc8f0a8303a7b5cc3fa07109c2e346cec7c0a16b40de462992ce943', $response->data[0]->hash);
    }

    public function testCreateHitsKeysEndpointAsPost(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(KeysCreateFixture::ATTRIBUTES, statusCode: 201);

        $response = $this->makeClient($http)->keys()->create([
            'name' => 'My New API Key',
            'limit' => 50,
            'limit_reset' => 'monthly',
        ]);

        $request = $http->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/keys', (string) $request->getUri());

        $body = json_decode((string) $request->getBody(), true);
        $this->assertSame('My New API Key', $body['name']);
        $this->assertSame(50, $body['limit']);
        $this->assertSame('monthly', $body['limit_reset']);

        $this->assertSame('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', $response->key);
        $this->assertSame('My New API Key', $response->data->name);
        $this->assertTrue($response->data->includeByokInLimit);
    }

    public function testCreateSerializesTypedRequest(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(KeysCreateFixture::ATTRIBUTES, statusCode: 201);

        $request = new CreateKeyRequest(
            name: 'My New API Key',
            limit: 50.0,
            limitReset: LimitReset::Monthly,
            expiresAt: '2027-12-31T23:59:59Z',
            includeByokInLimit: true,
        );

        $this->makeClient($http)->keys()->create($request);

        $body = json_decode((string) $http->lastRequest()->getBody(), true);
        $this->assertSame('My New API Key', $body['name']);
        $this->assertSame(50, $body['limit']);
        $this->assertSame('monthly', $body['limit_reset']);
        $this->assertSame('2027-12-31T23:59:59Z', $body['expires_at']);
        $this->assertTrue($body['include_byok_in_limit']);
    }

    public function testRetrieveHitsKeysHashEndpointAsGet(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(KeysRetrieveFixture::ATTRIBUTES);

        $response = $this->makeClient($http)->keys()->retrieve('f01d52606dc8f0a8303a7b5cc3fa07109c2e346cec7c0a16b40de462992ce943');

        $request = $http->lastRequest();
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/keys/f01d52606dc8f0a8303a7b5cc3fa07109c2e346cec7c0a16b40de462992ce943', (string) $request->getUri());

        $this->assertSame('My Production Key', $response->data->name);
    }

    public function testDeleteHitsKeysHashEndpointAsDelete(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(KeysDeleteFixture::ATTRIBUTES);

        $response = $this->makeClient($http)->keys()->delete('f01d52606dc8f0a8303a7b5cc3fa07109c2e346cec7c0a16b40de462992ce943');

        $request = $http->lastRequest();
        $this->assertSame('DELETE', $request->getMethod());
        $this->assertStringEndsWith('/keys/f01d52606dc8f0a8303a7b5cc3fa07109c2e346cec7c0a16b40de462992ce943', (string) $request->getUri());
        $this->assertSame('', (string) $request->getBody());

        $this->assertTrue($response->deleted);
    }

    public function testUpdateHitsKeysHashEndpointAsPatchWithBody(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(KeysRetrieveFixture::ATTRIBUTES);

        $request = new UpdateKeyRequest(
            name: 'Renamed Key',
            disabled: true,
            limit: 200.0,
            limitReset: LimitReset::Weekly,
        );

        $response = $this->makeClient($http)->keys()->update('somehash', $request);

        $httpRequest = $http->lastRequest();
        $this->assertSame('PATCH', $httpRequest->getMethod());
        $this->assertStringEndsWith('/keys/somehash', (string) $httpRequest->getUri());

        $body = json_decode((string) $httpRequest->getBody(), true);
        $this->assertSame('Renamed Key', $body['name']);
        $this->assertTrue($body['disabled']);
        $this->assertSame(200, $body['limit']);
        $this->assertSame('weekly', $body['limit_reset']);

        $this->assertSame('My Production Key', $response->data->name);
    }

    public function testCreateKeyRequestRejectsEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CreateKeyRequest(name: '');
    }

    public function testRetrieveRejectsEmptyHash(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $http = new RecordingHttpClient();
        $this->makeClient($http)->keys()->retrieve('');
    }
}
