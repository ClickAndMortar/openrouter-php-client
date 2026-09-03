<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Resources;

use OpenRouter\Enums\Workspaces\BudgetInterval;
use OpenRouter\Factory;
use OpenRouter\Responses\Workspaces\AddWorkspaceMembersResponse;
use OpenRouter\Responses\Workspaces\DeleteWorkspaceResponse;
use OpenRouter\Responses\Workspaces\ListWorkspaceBudgetsResponse;
use OpenRouter\Responses\Workspaces\ListWorkspaceMembersResponse;
use OpenRouter\Responses\Workspaces\ListWorkspacesResponse;
use OpenRouter\Responses\Workspaces\RemoveWorkspaceMembersResponse;
use OpenRouter\Responses\Workspaces\WorkspaceBudgetResponse;
use OpenRouter\Responses\Workspaces\WorkspaceResponse;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use OpenRouter\Tests\Fixtures\WorkspaceBudgetsFixture;
use OpenRouter\Tests\Fixtures\WorkspaceMembersFixture;
use OpenRouter\Tests\Fixtures\WorkspaceRetrieveFixture;
use OpenRouter\Tests\Fixtures\WorkspacesListFixture;
use OpenRouter\ValueObjects\Workspaces\CreateWorkspaceRequest;
use OpenRouter\ValueObjects\Workspaces\UpdateWorkspaceRequest;
use PHPUnit\Framework\TestCase;

final class WorkspacesTest extends TestCase
{
    private function client(RecordingHttpClient $http): \OpenRouter\Client
    {
        return (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();
    }

    public function testListPaginatesAndReturnsTypedWorkspaces(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(WorkspacesListFixture::ATTRIBUTES);

        $response = $this->client($http)->workspaces()->list(limit: 10, offset: 20);

        $request = $http->lastRequest();
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringContainsString('/workspaces', (string) $request->getUri());

        $query = [];
        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame('10', $query['limit']);
        $this->assertSame('20', $query['offset']);

        $this->assertInstanceOf(ListWorkspacesResponse::class, $response);
        $this->assertSame(1, $response->totalCount);
        $this->assertCount(1, $response->data);

        $workspace = $response->data[0];
        $this->assertSame('ws_01HQ8Z3K4M5N6P7Q8R9S', $workspace->id);
        $this->assertSame('platform-team', $workspace->slug);
        $this->assertSame('openai/gpt-4o', $workspace->defaultTextModel);
        $this->assertNull($workspace->defaultImageModel);
        $this->assertTrue($workspace->isObservabilityIoLoggingEnabled);
        $this->assertSame(0.25, $workspace->ioLoggingSamplingRate);
        $this->assertSame(['key_1'], $workspace->ioLoggingApiKeyIds);
    }

    public function testCreatePostsTheRequestBody(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(WorkspaceRetrieveFixture::ATTRIBUTES);

        $response = $this->client($http)->workspaces()->create(new CreateWorkspaceRequest(
            name: 'Platform team',
            slug: 'platform-team',
            description: 'Shared workspace',
            defaultTextModel: 'openai/gpt-4o',
            ioLoggingSamplingRate: 0.25,
        ));

        $request = $http->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/workspaces', (string) $request->getUri());

        $body = json_decode((string) $request->getBody(), true);
        $this->assertSame('Platform team', $body['name']);
        $this->assertSame('platform-team', $body['slug']);
        $this->assertSame('openai/gpt-4o', $body['default_text_model']);
        $this->assertSame(0.25, $body['io_logging_sampling_rate']);

        $this->assertInstanceOf(WorkspaceResponse::class, $response);
        $this->assertSame('Platform team', $response->data->name);
    }

    public function testCreateRejectsAnEmptySlug(): void
    {
        $this->expectException(\OpenRouter\Exceptions\InvalidArgumentException::class);

        new CreateWorkspaceRequest(name: 'Platform team', slug: '');
    }

    public function testRetrieveAndUpdateAndDelete(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(WorkspaceRetrieveFixture::ATTRIBUTES);
        $http->enqueueJson(WorkspaceRetrieveFixture::ATTRIBUTES);
        $http->enqueueJson(['deleted' => true]);

        $workspaces = $this->client($http)->workspaces();

        $workspaces->retrieve('ws_1');
        $this->assertSame('GET', $http->lastRequest()->getMethod());
        $this->assertStringEndsWith('/workspaces/ws_1', (string) $http->lastRequest()->getUri());

        $workspaces->update('ws_1', new UpdateWorkspaceRequest(description: 'Updated'));
        $this->assertSame('PATCH', $http->lastRequest()->getMethod());
        $this->assertSame(['description' => 'Updated'], json_decode((string) $http->lastRequest()->getBody(), true));

        $deleted = $workspaces->delete('ws_1');
        $this->assertSame('DELETE', $http->lastRequest()->getMethod());
        $this->assertInstanceOf(DeleteWorkspaceResponse::class, $deleted);
        $this->assertTrue($deleted->deleted);
    }

    public function testDeleteForwardsTheDefaultWorkspaceConfirmation(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['deleted' => true]);

        $this->client($http)->workspaces()->delete('ws_1', confirmDefaultWorkspaceDeletion: true);

        $this->assertStringContainsString(
            'confirm_default_workspace_deletion=true',
            (string) $http->lastRequest()->getUri(),
        );
    }

    public function testListBudgetsReturnsTypedBudgets(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(WorkspaceBudgetsFixture::ATTRIBUTES);

        $response = $this->client($http)->workspaces()->listBudgets('ws_1');

        $this->assertStringEndsWith('/workspaces/ws_1/budgets', (string) $http->lastRequest()->getUri());
        $this->assertInstanceOf(ListWorkspaceBudgetsResponse::class, $response);
        $this->assertTrue($response->includeByokInBudgets);
        $this->assertSame(250.0, $response->data[0]->limitUsd);
        $this->assertSame('monthly', $response->data[0]->resetInterval);
    }

    public function testSetBudgetPutsTheLimit(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson([
            'data' => WorkspaceBudgetsFixture::ATTRIBUTES['data'][0],
            'include_byok_in_budgets' => true,
        ]);

        $response = $this->client($http)->workspaces()->setBudget(
            'ws_1',
            BudgetInterval::Monthly,
            limitUsd: 250.0,
            includeByokInBudgets: true,
        );

        $request = $http->lastRequest();
        $this->assertSame('PUT', $request->getMethod());
        $this->assertStringEndsWith('/workspaces/ws_1/budgets/monthly', (string) $request->getUri());
        $body = json_decode((string) $request->getBody(), true);
        // JSON has no int/float distinction, so 250.0 goes out as `250`.
        $this->assertEqualsWithDelta(250.0, $body['limit_usd'], 0.0001);
        $this->assertTrue($body['include_byok_in_budgets']);
        $this->assertSame(['limit_usd', 'include_byok_in_budgets'], array_keys($body));

        $this->assertInstanceOf(WorkspaceBudgetResponse::class, $response);
        $this->assertSame(250.0, $response->data->limitUsd);
    }

    public function testRetrieveAndDeleteBudgetAcceptAStringInterval(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['data' => WorkspaceBudgetsFixture::ATTRIBUTES['data'][0]]);
        $http->enqueueJson(['deleted' => true]);

        $workspaces = $this->client($http)->workspaces();

        $workspaces->retrieveBudget('ws_1', 'weekly');
        $this->assertStringEndsWith('/workspaces/ws_1/budgets/weekly', (string) $http->lastRequest()->getUri());

        $deleted = $workspaces->deleteBudget('ws_1', BudgetInterval::Daily);
        $this->assertSame('DELETE', $http->lastRequest()->getMethod());
        $this->assertStringEndsWith('/workspaces/ws_1/budgets/daily', (string) $http->lastRequest()->getUri());
        $this->assertTrue($deleted->deleted);
    }

    public function testListMembersReturnsTypedMembers(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(WorkspaceMembersFixture::ATTRIBUTES);

        $response = $this->client($http)->workspaces()->listMembers('ws_1', limit: 5);

        $this->assertStringContainsString('/workspaces/ws_1/members', (string) $http->lastRequest()->getUri());
        $this->assertInstanceOf(ListWorkspaceMembersResponse::class, $response);
        $this->assertSame(1, $response->totalCount);
        $this->assertSame('admin', $response->data[0]->role);
        $this->assertSame('user_01HQ8Z3K4M5N6P7Q8R9S', $response->data[0]->userId);
    }

    public function testAddAndRemoveMembers(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson([
            'data' => WorkspaceMembersFixture::ATTRIBUTES['data'],
            'added_count' => 1,
        ]);
        $http->enqueueJson(['removed_count' => 2]);

        $workspaces = $this->client($http)->workspaces();

        $added = $workspaces->addMembers('ws_1', ['user_1']);
        $this->assertSame('POST', $http->lastRequest()->getMethod());
        $this->assertStringEndsWith('/workspaces/ws_1/members/add', (string) $http->lastRequest()->getUri());
        $this->assertSame(['user_ids' => ['user_1']], json_decode((string) $http->lastRequest()->getBody(), true));
        $this->assertInstanceOf(AddWorkspaceMembersResponse::class, $added);
        $this->assertSame(1, $added->addedCount);
        $this->assertCount(1, $added->data);

        $removed = $workspaces->removeMembers('ws_1', ['user_1', 'user_2']);
        $this->assertStringEndsWith('/workspaces/ws_1/members/remove', (string) $http->lastRequest()->getUri());
        $this->assertInstanceOf(RemoveWorkspaceMembersResponse::class, $removed);
        $this->assertSame(2, $removed->removedCount);
    }

    public function testWorkspaceKeepsUnknownFieldsInExtras(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['data' => [...WorkspaceRetrieveFixture::ATTRIBUTES['data'], 'a_new_field' => 1]]);

        $response = $this->client($http)->workspaces()->retrieve('ws_1');

        $this->assertSame(1, $response->data->extras['a_new_field']);
    }
}
