<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Resources;

use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\Factory;
use OpenRouter\Responses\Byok\ByokKeyResponse;
use OpenRouter\Responses\Byok\DeleteByokKeyResponse;
use OpenRouter\Responses\Byok\ListByokKeysResponse;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use OpenRouter\Tests\Fixtures\ByokListFixture;
use OpenRouter\ValueObjects\Byok\CreateByokKeyRequest;
use OpenRouter\ValueObjects\Byok\UpdateByokKeyRequest;
use PHPUnit\Framework\TestCase;

final class ByokTest extends TestCase
{
    private function client(RecordingHttpClient $http): \OpenRouter\Client
    {
        return (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();
    }

    public function testListFiltersByProviderAndWorkspace(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ByokListFixture::ATTRIBUTES);

        $response = $this->client($http)->byok()->list(
            limit: 10,
            offset: 0,
            workspaceId: 'ws_1',
            provider: 'anthropic',
        );

        $query = [];
        parse_str($http->lastRequest()->getUri()->getQuery(), $query);
        $this->assertSame('10', $query['limit']);
        $this->assertSame('ws_1', $query['workspace_id']);
        $this->assertSame('anthropic', $query['provider']);

        $this->assertInstanceOf(ListByokKeysResponse::class, $response);
        $this->assertSame(1, $response->totalCount);

        $key = $response->data[0];
        $this->assertSame('anthropic', $key->provider);
        $this->assertSame('sk-ant-...4f2a', $key->label);
        $this->assertFalse($key->disabled);
        $this->assertTrue($key->isFallback);
        $this->assertSame(['anthropic/claude-sonnet-4'], $key->allowedModels);
        $this->assertNull($key->allowedUserIds);
        $this->assertSame(1, $key->sortOrder);
    }

    public function testCreateSendsTheProviderAndSecret(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['data' => ByokListFixture::ATTRIBUTES['data'][0]]);

        $response = $this->client($http)->byok()->create(new CreateByokKeyRequest(
            provider: 'anthropic',
            key: 'sk-ant-secret',
            name: 'Anthropic production',
            isFallback: true,
            allowedModels: ['anthropic/claude-sonnet-4'],
        ));

        $request = $http->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/byok', (string) $request->getUri());

        $body = json_decode((string) $request->getBody(), true);
        $this->assertSame('anthropic', $body['provider']);
        $this->assertSame('sk-ant-secret', $body['key']);
        $this->assertTrue($body['is_fallback']);
        $this->assertSame(['anthropic/claude-sonnet-4'], $body['allowed_models']);

        $this->assertInstanceOf(ByokKeyResponse::class, $response);
        $this->assertSame('byok_01HQ8Z3K4M5N6P7Q8R9S', $response->data->id);
    }

    public function testCreateRejectsAnEmptyKey(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CreateByokKeyRequest(provider: 'anthropic', key: '');
    }

    public function testRetrieveUpdateAndDelete(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['data' => ByokListFixture::ATTRIBUTES['data'][0]]);
        $http->enqueueJson(['data' => ByokListFixture::ATTRIBUTES['data'][0]]);
        $http->enqueueJson(['deleted' => true]);

        $byok = $this->client($http)->byok();

        $byok->retrieve('byok_1');
        $this->assertSame('GET', $http->lastRequest()->getMethod());
        $this->assertStringEndsWith('/byok/byok_1', (string) $http->lastRequest()->getUri());

        $byok->update('byok_1', new UpdateByokKeyRequest(disabled: true));
        $this->assertSame('PATCH', $http->lastRequest()->getMethod());
        $this->assertSame(['disabled' => true], json_decode((string) $http->lastRequest()->getBody(), true));

        $deleted = $byok->delete('byok_1');
        $this->assertSame('DELETE', $http->lastRequest()->getMethod());
        $this->assertInstanceOf(DeleteByokKeyResponse::class, $deleted);
        $this->assertTrue($deleted->deleted);
    }

    public function testKeyKeepsUnknownFieldsInExtras(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['data' => [...ByokListFixture::ATTRIBUTES['data'][0], 'a_new_field' => true]]);

        $response = $this->client($http)->byok()->retrieve('byok_1');

        $this->assertTrue($response->data->extras['a_new_field']);
    }
}
