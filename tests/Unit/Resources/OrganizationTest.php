<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Resources;

use OpenRouter\Enums\Organization\MemberRole;
use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\Factory;
use OpenRouter\Responses\Organization\ListMembersResponse;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use OpenRouter\Tests\Fixtures\OrganizationMembersFixture;
use PHPUnit\Framework\TestCase;

final class OrganizationTest extends TestCase
{
    private function makeClient(RecordingHttpClient $http): \OpenRouter\Client
    {
        return (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();
    }

    public function testListMembersHitsEndpointAsGet(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(OrganizationMembersFixture::ATTRIBUTES);

        $this->makeClient($http)->organization()->listMembers();

        $request = $http->lastRequest();
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/organization/members', (string) $request->getUri());
        $this->assertSame('', (string) $request->getBody());
    }

    public function testListMembersPropagatesPaginationQuery(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(OrganizationMembersFixture::ATTRIBUTES);

        $this->makeClient($http)->organization()->listMembers(offset: 10, limit: 50);

        $uri = (string) $http->lastRequest()->getUri();
        $this->assertStringContainsString('offset=10', $uri);
        $this->assertStringContainsString('limit=50', $uri);
    }

    public function testListMembersReturnsTypedResponse(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(OrganizationMembersFixture::ATTRIBUTES);

        $response = $this->makeClient($http)->organization()->listMembers();

        $this->assertInstanceOf(ListMembersResponse::class, $response);
        $this->assertSame(25, $response->totalCount);
        $this->assertCount(2, $response->data);

        $jane = $response->data[0];
        $this->assertSame('user_2dHFtVWx2n56w6HkM0000000000', $jane->id);
        $this->assertSame('jane.doe@example.com', $jane->email);
        $this->assertSame('Jane', $jane->firstName);
        $this->assertSame('Doe', $jane->lastName);
        $this->assertSame(MemberRole::Member->value, $jane->role);

        $admin = $response->data[1];
        $this->assertNull($admin->firstName);
        $this->assertNull($admin->lastName);
        $this->assertSame(MemberRole::Admin->value, $admin->role);
    }

    public function testListMembersRejectsNegativeOffset(): void
    {
        $http = new RecordingHttpClient();

        $this->expectException(InvalidArgumentException::class);

        $this->makeClient($http)->organization()->listMembers(offset: -1);
    }

    public function testListMembersRejectsOutOfRangeLimit(): void
    {
        $http = new RecordingHttpClient();

        $this->expectException(InvalidArgumentException::class);

        $this->makeClient($http)->organization()->listMembers(limit: 101);
    }

    private const CREATED = [
        'created' => true,
        'organization' => [
            'id' => 'org_01HQ8Z3K4M5N6P7Q8R9S',
            'name' => '[Parent] Acme Corp',
            'slug' => 'parent-acme-corp',
            'email' => 'owner@acme.example',
        ],
        'grant' => [
            'id' => 'grant_01HQ8Z3K4M5N6P7Q8R9S',
            'scopes' => ['inference', 'keys_read'],
        ],
        'management_key' => [
            'name' => 'Acme Corp management key',
            'key' => 'sk-or-mgmt-plaintext-once',
        ],
    ];

    public function testCreatePostsNameAndEmail(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(self::CREATED);

        $response = $this->makeClient($http)->organization()->create('Acme Corp', 'owner@acme.example');

        $request = $http->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/organization', (string) $request->getUri());
        $this->assertSame(
            ['name' => 'Acme Corp', 'email' => 'owner@acme.example'],
            json_decode((string) $request->getBody(), true),
        );

        $this->assertTrue($response->created);
        $this->assertSame('org_01HQ8Z3K4M5N6P7Q8R9S', $response->organization->id);
        $this->assertSame('parent-acme-corp', $response->organization->slug);
        $this->assertSame(['inference', 'keys_read'], $response->grant->scopes);
        $this->assertSame('sk-or-mgmt-plaintext-once', $response->managementKey?->key);
    }

    public function testCreateHandlesAnIdempotentReplayWithoutAKey(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson([...self::CREATED, 'created' => false, 'management_key' => null]);

        $response = $this->makeClient($http)->organization()->create('Acme Corp', 'owner@acme.example');

        // A repeat call returns the existing organization and no key, because a
        // delivered management key is never retrievable again.
        $this->assertFalse($response->created);
        $this->assertNull($response->managementKey);
        $this->assertSame('org_01HQ8Z3K4M5N6P7Q8R9S', $response->organization->id);
    }

    public function testCreateRejectsAnEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->makeClient(new RecordingHttpClient())->organization()->create('  ', 'owner@acme.example');
    }

    public function testCreateRejectsAnEmptyEmail(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->makeClient(new RecordingHttpClient())->organization()->create('Acme Corp', '');
    }

    public function testCreateKeepsUnknownFieldsInExtras(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson([...self::CREATED, 'a_new_field' => 7]);

        $response = $this->makeClient($http)->organization()->create('Acme Corp', 'owner@acme.example');

        $this->assertSame(7, $response->extras['a_new_field']);
        $this->assertSame(7, $response->toArray()['a_new_field']);
    }
}
