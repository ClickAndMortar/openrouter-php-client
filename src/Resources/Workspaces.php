<?php

declare(strict_types=1);

namespace OpenRouter\Resources;

use OpenRouter\Contracts\Resources\WorkspacesContract;
use OpenRouter\Enums\Workspaces\BudgetInterval;
use OpenRouter\Responses\Workspaces\AddWorkspaceMembersResponse;
use OpenRouter\Responses\Workspaces\DeleteWorkspaceBudgetResponse;
use OpenRouter\Responses\Workspaces\DeleteWorkspaceResponse;
use OpenRouter\Responses\Workspaces\ListWorkspaceBudgetsResponse;
use OpenRouter\Responses\Workspaces\ListWorkspaceMembersResponse;
use OpenRouter\Responses\Workspaces\ListWorkspacesResponse;
use OpenRouter\Responses\Workspaces\RemoveWorkspaceMembersResponse;
use OpenRouter\Responses\Workspaces\WorkspaceBudgetResponse;
use OpenRouter\Responses\Workspaces\WorkspaceResponse;
use OpenRouter\ValueObjects\Transporter\Payload;
use OpenRouter\ValueObjects\Workspaces\CreateWorkspaceRequest;
use OpenRouter\ValueObjects\Workspaces\UpdateWorkspaceRequest;

final class Workspaces implements WorkspacesContract
{
    use Concerns\Paginates;
    use Concerns\Transportable;

    public function list(?int $limit = null, ?int $offset = null): ListWorkspacesResponse
    {
        $response = $this->transporter->requestObject(
            Payload::list('workspaces', self::page($limit, $offset)),
        );

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return ListWorkspacesResponse::from($data, $response->meta());
    }

    /**
     * @param  CreateWorkspaceRequest|array<string, mixed>  $parameters
     */
    public function create(CreateWorkspaceRequest|array $parameters): WorkspaceResponse
    {
        $params = $parameters instanceof CreateWorkspaceRequest ? $parameters->toArray() : $parameters;

        $response = $this->transporter->requestObject(Payload::create('workspaces', $params));

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return WorkspaceResponse::from($data, $response->meta());
    }

    public function retrieve(string $workspaceId): WorkspaceResponse
    {
        $response = $this->transporter->requestObject(Payload::retrieve('workspaces', $workspaceId));

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return WorkspaceResponse::from($data, $response->meta());
    }

    /**
     * @param  UpdateWorkspaceRequest|array<string, mixed>  $parameters
     */
    public function update(string $workspaceId, UpdateWorkspaceRequest|array $parameters): WorkspaceResponse
    {
        $params = $parameters instanceof UpdateWorkspaceRequest ? $parameters->toArray() : $parameters;

        $response = $this->transporter->requestObject(Payload::modify('workspaces', $workspaceId, $params));

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return WorkspaceResponse::from($data, $response->meta());
    }

    public function delete(string $workspaceId, bool $confirmDefaultWorkspaceDeletion = false): DeleteWorkspaceResponse
    {
        $query = $confirmDefaultWorkspaceDeletion ? ['confirm_default_workspace_deletion' => 'true'] : [];

        $response = $this->transporter->requestObject(
            Payload::delete('workspaces', $workspaceId, query: $query),
        );

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return DeleteWorkspaceResponse::from($data, $response->meta());
    }

    public function listBudgets(string $workspaceId): ListWorkspaceBudgetsResponse
    {
        $response = $this->transporter->requestObject(
            Payload::retrieve('workspaces', $workspaceId, '/budgets'),
        );

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return ListWorkspaceBudgetsResponse::from($data, $response->meta());
    }

    public function retrieveBudget(string $workspaceId, BudgetInterval|string $interval): WorkspaceBudgetResponse
    {
        $response = $this->transporter->requestObject(
            Payload::retrieve('workspaces', $workspaceId, '/budgets/'.self::interval($interval)),
        );

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return WorkspaceBudgetResponse::from($data, $response->meta());
    }

    public function setBudget(
        string $workspaceId,
        BudgetInterval|string $interval,
        float $limitUsd,
        ?bool $includeByokInBudgets = null,
    ): WorkspaceBudgetResponse {
        $body = ['limit_usd' => $limitUsd];

        if ($includeByokInBudgets !== null) {
            $body['include_byok_in_budgets'] = $includeByokInBudgets;
        }

        $payload = Payload::put("workspaces/{$workspaceId}/budgets/".self::interval($interval), $body);

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return WorkspaceBudgetResponse::from($data, $response->meta());
    }

    public function deleteBudget(string $workspaceId, BudgetInterval|string $interval): DeleteWorkspaceBudgetResponse
    {
        $payload = Payload::delete('workspaces', $workspaceId, '/budgets/'.self::interval($interval));

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return DeleteWorkspaceBudgetResponse::from($data, $response->meta());
    }

    public function listMembers(string $workspaceId, ?int $limit = null, ?int $offset = null): ListWorkspaceMembersResponse
    {
        $payload = Payload::retrieve('workspaces', $workspaceId, '/members', self::page($limit, $offset));

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return ListWorkspaceMembersResponse::from($data, $response->meta());
    }

    /**
     * @param  list<string>  $userIds
     */
    public function addMembers(string $workspaceId, array $userIds): AddWorkspaceMembersResponse
    {
        $payload = Payload::create("workspaces/{$workspaceId}/members/add", ['user_ids' => $userIds]);

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return AddWorkspaceMembersResponse::from($data, $response->meta());
    }

    /**
     * @param  list<string>  $userIds
     */
    public function removeMembers(string $workspaceId, array $userIds): RemoveWorkspaceMembersResponse
    {
        $payload = Payload::create("workspaces/{$workspaceId}/members/remove", ['user_ids' => $userIds]);

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return RemoveWorkspaceMembersResponse::from($data, $response->meta());
    }

    private static function interval(BudgetInterval|string $interval): string
    {
        return $interval instanceof BudgetInterval ? $interval->value : $interval;
    }
}
