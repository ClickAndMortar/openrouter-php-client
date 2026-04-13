<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Resources;

use OpenRouter\Enums\Guardrails\GuardrailInterval;
use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\Factory;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use OpenRouter\Tests\Fixtures\GuardrailsBulkAssignKeysFixture;
use OpenRouter\Tests\Fixtures\GuardrailsBulkAssignMembersFixture;
use OpenRouter\Tests\Fixtures\GuardrailsBulkUnassignKeysFixture;
use OpenRouter\Tests\Fixtures\GuardrailsBulkUnassignMembersFixture;
use OpenRouter\Tests\Fixtures\GuardrailsCreateFixture;
use OpenRouter\Tests\Fixtures\GuardrailsDeleteFixture;
use OpenRouter\Tests\Fixtures\GuardrailsListFixture;
use OpenRouter\Tests\Fixtures\GuardrailsListKeyAssignmentsFixture;
use OpenRouter\Tests\Fixtures\GuardrailsListMemberAssignmentsFixture;
use OpenRouter\Tests\Fixtures\GuardrailsRetrieveFixture;
use OpenRouter\Tests\Fixtures\GuardrailsUpdateFixture;
use OpenRouter\ValueObjects\Guardrails\CreateGuardrailRequest;
use OpenRouter\ValueObjects\Guardrails\UpdateGuardrailRequest;
use PHPUnit\Framework\TestCase;

final class GuardrailsTest extends TestCase
{
    private const GUARDRAIL_ID = '550e8400-e29b-41d4-a716-446655440000';

    private function makeClient(RecordingHttpClient $http): \OpenRouter\Client
    {
        return (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();
    }

    public function testListHitsGuardrailsEndpointAsGetWithQuery(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(GuardrailsListFixture::ATTRIBUTES);

        $response = $this->makeClient($http)->guardrails()->list(offset: 10, limit: 25);

        $request = $http->lastRequest();
        $this->assertSame('GET', $request->getMethod());
        $uri = (string) $request->getUri();
        $this->assertStringContainsString('/guardrails?', $uri);
        $this->assertStringContainsString('offset=10', $uri);
        $this->assertStringContainsString('limit=25', $uri);

        $this->assertSame(1, $response->totalCount);
        $this->assertCount(1, $response->data);
        $this->assertSame('Production Guardrail', $response->data[0]->name);
        $this->assertSame(100.0, $response->data[0]->limitUsd);
        $this->assertSame('monthly', $response->data[0]->resetInterval);
        $this->assertSame(['openai', 'anthropic', 'google'], $response->data[0]->allowedProviders);
        $this->assertNull($response->data[0]->allowedModels);
    }

    public function testCreateHitsGuardrailsEndpointAsPost(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(GuardrailsCreateFixture::ATTRIBUTES, statusCode: 201);

        $response = $this->makeClient($http)->guardrails()->create([
            'name' => 'My New Guardrail',
            'limit_usd' => 50,
            'reset_interval' => 'monthly',
        ]);

        $request = $http->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/guardrails', (string) $request->getUri());

        $body = json_decode((string) $request->getBody(), true);
        $this->assertSame('My New Guardrail', $body['name']);
        $this->assertSame(50, $body['limit_usd']);
        $this->assertSame('monthly', $body['reset_interval']);

        $this->assertSame('My New Guardrail', $response->data->name);
        $this->assertSame(self::GUARDRAIL_ID, $response->data->id);
        $this->assertNull($response->data->updatedAt);
    }

    public function testCreateSerializesTypedRequest(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(GuardrailsCreateFixture::ATTRIBUTES, statusCode: 201);

        $payload = new CreateGuardrailRequest(
            name: 'My New Guardrail',
            description: 'A guardrail for limiting API usage',
            limitUsd: 50.0,
            resetInterval: GuardrailInterval::Monthly,
            enforceZdr: false,
            allowedProviders: ['openai', 'anthropic', 'deepseek'],
        );

        $this->makeClient($http)->guardrails()->create($payload);

        $body = json_decode((string) $http->lastRequest()->getBody(), true);
        $this->assertSame('My New Guardrail', $body['name']);
        $this->assertSame('A guardrail for limiting API usage', $body['description']);
        $this->assertSame(50, $body['limit_usd']);
        $this->assertSame('monthly', $body['reset_interval']);
        $this->assertFalse($body['enforce_zdr']);
        $this->assertSame(['openai', 'anthropic', 'deepseek'], $body['allowed_providers']);
        $this->assertArrayNotHasKey('allowed_models', $body);
    }

    public function testRetrieveHitsGuardrailsIdEndpointAsGet(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(GuardrailsRetrieveFixture::ATTRIBUTES);

        $response = $this->makeClient($http)->guardrails()->retrieve(self::GUARDRAIL_ID);

        $request = $http->lastRequest();
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/guardrails/'.self::GUARDRAIL_ID, (string) $request->getUri());

        $this->assertSame('Production Guardrail', $response->data->name);
        $this->assertSame('2025-08-24T15:45:00Z', $response->data->updatedAt);
    }

    public function testUpdateHitsGuardrailsIdEndpointAsPatchWithBody(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(GuardrailsUpdateFixture::ATTRIBUTES);

        $payload = new UpdateGuardrailRequest(
            name: 'Updated Guardrail Name',
            description: 'Updated description',
            limitUsd: 75.0,
            resetInterval: GuardrailInterval::Weekly,
        );

        $response = $this->makeClient($http)->guardrails()->update(self::GUARDRAIL_ID, $payload);

        $request = $http->lastRequest();
        $this->assertSame('PATCH', $request->getMethod());
        $this->assertStringEndsWith('/guardrails/'.self::GUARDRAIL_ID, (string) $request->getUri());

        $body = json_decode((string) $request->getBody(), true);
        $this->assertSame('Updated Guardrail Name', $body['name']);
        $this->assertSame('Updated description', $body['description']);
        $this->assertSame(75, $body['limit_usd']);
        $this->assertSame('weekly', $body['reset_interval']);

        $this->assertSame('Updated Guardrail Name', $response->data->name);
        $this->assertSame(75.0, $response->data->limitUsd);
        $this->assertTrue($response->data->enforceZdr);
    }

    public function testDeleteHitsGuardrailsIdEndpointAsDelete(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(GuardrailsDeleteFixture::ATTRIBUTES);

        $response = $this->makeClient($http)->guardrails()->delete(self::GUARDRAIL_ID);

        $request = $http->lastRequest();
        $this->assertSame('DELETE', $request->getMethod());
        $this->assertStringEndsWith('/guardrails/'.self::GUARDRAIL_ID, (string) $request->getUri());
        $this->assertSame('', (string) $request->getBody());

        $this->assertTrue($response->deleted);
    }

    public function testListKeyAssignmentsHitsScopedEndpointAsGet(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(GuardrailsListKeyAssignmentsFixture::ATTRIBUTES);

        $response = $this->makeClient($http)
            ->guardrails()
            ->listKeyAssignments(self::GUARDRAIL_ID, offset: 0, limit: 50);

        $request = $http->lastRequest();
        $this->assertSame('GET', $request->getMethod());
        $uri = (string) $request->getUri();
        $this->assertStringContainsString('/guardrails/'.self::GUARDRAIL_ID.'/assignments/keys?', $uri);
        $this->assertStringContainsString('offset=0', $uri);
        $this->assertStringContainsString('limit=50', $uri);

        $this->assertSame(1, $response->totalCount);
        $this->assertSame('Production Key', $response->data[0]->keyName);
        $this->assertSame('prod-key', $response->data[0]->keyLabel);
    }

    public function testBulkAssignKeysHitsScopedEndpointAsPost(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(GuardrailsBulkAssignKeysFixture::ATTRIBUTES);

        $response = $this->makeClient($http)
            ->guardrails()
            ->bulkAssignKeys(self::GUARDRAIL_ID, ['hash1', 'hash2', 'hash3']);

        $request = $http->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/guardrails/'.self::GUARDRAIL_ID.'/assignments/keys', (string) $request->getUri());

        $body = json_decode((string) $request->getBody(), true);
        $this->assertSame(['hash1', 'hash2', 'hash3'], $body['key_hashes']);

        $this->assertSame(3, $response->assignedCount);
    }

    public function testBulkUnassignKeysHitsScopedRemoveEndpointAsPost(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(GuardrailsBulkUnassignKeysFixture::ATTRIBUTES);

        $response = $this->makeClient($http)
            ->guardrails()
            ->bulkUnassignKeys(self::GUARDRAIL_ID, ['hash1', 'hash2', 'hash3']);

        $request = $http->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/guardrails/'.self::GUARDRAIL_ID.'/assignments/keys/remove', (string) $request->getUri());

        $body = json_decode((string) $request->getBody(), true);
        $this->assertSame(['hash1', 'hash2', 'hash3'], $body['key_hashes']);

        $this->assertSame(3, $response->unassignedCount);
    }

    public function testListMemberAssignmentsHitsScopedEndpointAsGet(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(GuardrailsListMemberAssignmentsFixture::ATTRIBUTES);

        $response = $this->makeClient($http)
            ->guardrails()
            ->listMemberAssignments(self::GUARDRAIL_ID);

        $request = $http->lastRequest();
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/guardrails/'.self::GUARDRAIL_ID.'/assignments/members', (string) $request->getUri());

        $this->assertSame(1, $response->totalCount);
        $this->assertSame('user_abc123', $response->data[0]->userId);
        $this->assertSame('org_xyz789', $response->data[0]->organizationId);
    }

    public function testBulkAssignMembersHitsScopedEndpointAsPost(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(GuardrailsBulkAssignMembersFixture::ATTRIBUTES);

        $response = $this->makeClient($http)
            ->guardrails()
            ->bulkAssignMembers(self::GUARDRAIL_ID, ['user_abc123', 'user_def456']);

        $request = $http->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/guardrails/'.self::GUARDRAIL_ID.'/assignments/members', (string) $request->getUri());

        $body = json_decode((string) $request->getBody(), true);
        $this->assertSame(['user_abc123', 'user_def456'], $body['member_user_ids']);

        $this->assertSame(2, $response->assignedCount);
    }

    public function testBulkUnassignMembersHitsScopedRemoveEndpointAsPost(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(GuardrailsBulkUnassignMembersFixture::ATTRIBUTES);

        $response = $this->makeClient($http)
            ->guardrails()
            ->bulkUnassignMembers(self::GUARDRAIL_ID, ['user_abc123', 'user_def456']);

        $request = $http->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/guardrails/'.self::GUARDRAIL_ID.'/assignments/members/remove', (string) $request->getUri());

        $body = json_decode((string) $request->getBody(), true);
        $this->assertSame(['user_abc123', 'user_def456'], $body['member_user_ids']);

        $this->assertSame(2, $response->unassignedCount);
    }

    public function testListAllKeyAssignmentsHitsUnscopedEndpointAsGet(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(GuardrailsListKeyAssignmentsFixture::ATTRIBUTES);

        $response = $this->makeClient($http)->guardrails()->listAllKeyAssignments(limit: 10);

        $request = $http->lastRequest();
        $this->assertSame('GET', $request->getMethod());
        $uri = (string) $request->getUri();
        $this->assertStringContainsString('/guardrails/assignments/keys?', $uri);
        $this->assertStringContainsString('limit=10', $uri);
        $this->assertStringNotContainsString('offset=', $uri);

        $this->assertSame(1, $response->totalCount);
        $this->assertSame('Production Key', $response->data[0]->keyName);
    }

    public function testListAllMemberAssignmentsHitsUnscopedEndpointAsGet(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(GuardrailsListMemberAssignmentsFixture::ATTRIBUTES);

        $response = $this->makeClient($http)->guardrails()->listAllMemberAssignments();

        $request = $http->lastRequest();
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/guardrails/assignments/members', (string) $request->getUri());

        $this->assertSame(1, $response->totalCount);
        $this->assertSame('user_abc123', $response->data[0]->userId);
    }

    public function testCreateGuardrailRequestRejectsEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CreateGuardrailRequest(name: '');
    }

    public function testRetrieveRejectsEmptyId(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $http = new RecordingHttpClient();
        $this->makeClient($http)->guardrails()->retrieve('');
    }

    public function testBulkAssignKeysRejectsEmptyList(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $http = new RecordingHttpClient();
        $this->makeClient($http)->guardrails()->bulkAssignKeys(self::GUARDRAIL_ID, []);
    }

    public function testListRejectsOutOfRangeLimit(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $http = new RecordingHttpClient();
        $this->makeClient($http)->guardrails()->list(limit: 101);
    }
}
