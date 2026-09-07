<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Resources;

use OpenRouter\Enums\Scim\ScimSyncJobStatus;
use OpenRouter\Enums\Workspaces\WorkspaceRole;
use OpenRouter\Factory;
use OpenRouter\Responses\Scim\DeleteScimGroupMappingResponse;
use OpenRouter\Responses\Scim\ScimSyncJobResponse;
use OpenRouter\Responses\Scim\ListScimGroupMappingsResponse;
use OpenRouter\Responses\Scim\ListScimGroupsResponse;
use OpenRouter\Responses\Scim\ScimGroupMappingResponse;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use PHPUnit\Framework\TestCase;

final class ScimTest extends TestCase
{
    private const GROUP = [
        'id' => 'sg_01HQ8Z3K4M5N6P7Q8R9S',
        'organization_id' => 'org_01HQ8Z3K4M5N6P7Q8R9S',
        'display_name' => 'Engineering',
        'external_id' => 'okta-group-42',
        'created_at' => '2026-01-04T10:00:00Z',
        'updated_at' => '2026-02-01T09:30:00Z',
    ];

    private const MAPPING = [
        'id' => 'sgm_01HQ8Z3K4M5N6P7Q8R9S',
        'organization_id' => 'org_01HQ8Z3K4M5N6P7Q8R9S',
        'scim_group_id' => 'sg_01HQ8Z3K4M5N6P7Q8R9S',
        'workspace_id' => 'ws_01HQ8Z3K4M5N6P7Q8R9S',
        'role' => 'member',
        'created_at' => '2026-01-04T10:00:00Z',
        'updated_at' => '2026-02-01T09:30:00Z',
    ];

    private const SYNC_JOB = [
        'id' => '9f1c2d3e-4a5b-6c7d-8e9f-0a1b2c3d4e5f',
        'status' => 'queued',
        'synced_groups' => null,
        'deleted_groups' => null,
        'error_message' => null,
        'created_at' => '2026-09-07T06:00:00Z',
        'started_at' => null,
        'finished_at' => null,
    ];

    private function client(RecordingHttpClient $http): \OpenRouter\Client
    {
        return (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();
    }

    public function testListGroupsReturnsTypedGroups(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['data' => [self::GROUP], 'total_count' => 1]);

        $response = $this->client($http)->scim()->listGroups(limit: 25);

        $this->assertStringContainsString('/scim/groups', (string) $http->lastRequest()->getUri());
        $this->assertInstanceOf(ListScimGroupsResponse::class, $response);
        $this->assertSame(1, $response->totalCount);
        $this->assertSame('Engineering', $response->data[0]->displayName);
        $this->assertSame('okta-group-42', $response->data[0]->externalId);
    }

    public function testListMappingsReturnsTypedMappings(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['data' => [self::MAPPING], 'total_count' => 1]);

        $response = $this->client($http)->scim()->listGroupMappings();

        $this->assertStringContainsString('/scim/group-mappings', (string) $http->lastRequest()->getUri());
        $this->assertInstanceOf(ListScimGroupMappingsResponse::class, $response);
        $this->assertSame('member', $response->data[0]->role);
        $this->assertSame('ws_01HQ8Z3K4M5N6P7Q8R9S', $response->data[0]->workspaceId);
    }

    public function testCreateMappingPostsTheBody(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['data' => self::MAPPING]);

        $response = $this->client($http)->scim()->createGroupMapping(
            scimGroupId: 'sg_1',
            workspaceId: 'ws_1',
            role: WorkspaceRole::Member,
        );

        $request = $http->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/scim/group-mappings', (string) $request->getUri());
        $this->assertSame(
            ['scim_group_id' => 'sg_1', 'workspace_id' => 'ws_1', 'role' => 'member'],
            json_decode((string) $request->getBody(), true),
        );
        $this->assertInstanceOf(ScimGroupMappingResponse::class, $response);
    }

    public function testRetrieveUpdateAndDeleteMapping(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['data' => self::MAPPING]);
        $http->enqueueJson(['data' => self::MAPPING]);
        $http->enqueueJson(['deleted' => true]);

        $scim = $this->client($http)->scim();

        $scim->retrieveGroupMapping('sgm_1');
        $this->assertStringEndsWith('/scim/group-mappings/sgm_1', (string) $http->lastRequest()->getUri());

        $scim->updateGroupMapping('sgm_1', WorkspaceRole::Admin);
        $this->assertSame('PATCH', $http->lastRequest()->getMethod());
        $this->assertSame(['role' => 'admin'], json_decode((string) $http->lastRequest()->getBody(), true));

        $deleted = $scim->deleteGroupMapping('sgm_1', keepMembers: true);
        $this->assertSame('DELETE', $http->lastRequest()->getMethod());
        $this->assertStringContainsString('keep_members=true', (string) $http->lastRequest()->getUri());
        $this->assertInstanceOf(DeleteScimGroupMappingResponse::class, $deleted);
        $this->assertTrue($deleted->deleted);
    }

    public function testDeleteAlwaysSendsKeepMembersBecauseTheApiRequiresIt(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['deleted' => true]);

        $this->client($http)->scim()->deleteGroupMapping('sgm_1');

        $this->assertStringContainsString('keep_members=false', (string) $http->lastRequest()->getUri());
    }

    public function testCreateSyncJobPostsAnEmptyBodyAndReturnsTheQueuedJob(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['data' => self::SYNC_JOB], 202, ['Location' => 'https://openrouter.ai/api/v1/scim/sync-jobs/'.self::SYNC_JOB['id']]);

        $response = $this->client($http)->scim()->createSyncJob();

        $request = $http->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/scim/sync-jobs', (string) $request->getUri());
        // The endpoint takes no request body, but still expects a JSON object.
        $this->assertSame('{}', (string) $request->getBody());

        $this->assertInstanceOf(ScimSyncJobResponse::class, $response);
        $this->assertSame(self::SYNC_JOB['id'], $response->data->id);
        $this->assertSame(ScimSyncJobStatus::Queued, $response->data->status);
        $this->assertNull($response->data->finishedAt);
    }

    public function testRetrieveSyncJobReadsACompletedJob(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['data' => [
            ...self::SYNC_JOB,
            'status' => 'succeeded',
            'synced_groups' => 12,
            'deleted_groups' => 3,
            'started_at' => '2026-09-07T06:00:05Z',
            'finished_at' => '2026-09-07T06:00:42Z',
        ]]);

        $response = $this->client($http)->scim()->retrieveSyncJob('9f1c2d3e-4a5b-6c7d-8e9f-0a1b2c3d4e5f');

        $this->assertSame('GET', $http->lastRequest()->getMethod());
        $this->assertStringEndsWith(
            '/scim/sync-jobs/9f1c2d3e-4a5b-6c7d-8e9f-0a1b2c3d4e5f',
            (string) $http->lastRequest()->getUri(),
        );

        $this->assertSame(ScimSyncJobStatus::Succeeded, $response->data->status);
        $this->assertSame(12, $response->data->syncedGroups);
        $this->assertSame(3, $response->data->deletedGroups);
        $this->assertSame('2026-09-07T06:00:42Z', $response->data->finishedAt);
        $this->assertNull($response->data->errorMessage);
    }

    public function testSyncJobExposesAFailureMessage(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['data' => [
            ...self::SYNC_JOB,
            'status' => 'failed',
            'error_message' => 'Directory unreachable',
        ]]);

        $response = $this->client($http)->scim()->retrieveSyncJob('job_1');

        $this->assertSame(ScimSyncJobStatus::Failed, $response->data->status);
        $this->assertSame('Directory unreachable', $response->data->errorMessage);
    }

    public function testSyncJobFallsBackToUnknownForAnUnrecognisedStatus(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['data' => [...self::SYNC_JOB, 'status' => 'paused']]);

        $response = $this->client($http)->scim()->retrieveSyncJob('job_1');

        // The spec marks status as an open enum, so a new value must not throw.
        $this->assertSame(ScimSyncJobStatus::Unknown, $response->data->status);
        $this->assertSame('paused', $response->data->rawStatus);
    }

    public function testSyncJobKeepsUnknownFieldsInExtras(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['data' => [...self::SYNC_JOB, 'a_new_field' => 'x']]);

        $response = $this->client($http)->scim()->retrieveSyncJob('job_1');

        $this->assertSame('x', $response->data->extras['a_new_field']);
        $this->assertSame('x', $response->toArray()['data']['a_new_field']);
    }
}
