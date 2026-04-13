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
}
