<?php

declare(strict_types=1);

namespace OpenRouter\Contracts\Resources;

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
use OpenRouter\ValueObjects\Workspaces\CreateWorkspaceRequest;
use OpenRouter\ValueObjects\Workspaces\UpdateWorkspaceRequest;

interface WorkspacesContract
{
    public function list(?int $limit = null, ?int $offset = null): ListWorkspacesResponse;

    /**
     * @param  CreateWorkspaceRequest|array<string, mixed>  $parameters
     */
    public function create(CreateWorkspaceRequest|array $parameters): WorkspaceResponse;

    public function retrieve(string $workspaceId): WorkspaceResponse;

    /**
     * @param  UpdateWorkspaceRequest|array<string, mixed>  $parameters
     */
    public function update(string $workspaceId, UpdateWorkspaceRequest|array $parameters): WorkspaceResponse;

    /**
     * Deleting the default workspace requires
     * `$confirmDefaultWorkspaceDeletion: true` as a deliberate guard.
     */
    public function delete(string $workspaceId, bool $confirmDefaultWorkspaceDeletion = false): DeleteWorkspaceResponse;

    public function listBudgets(string $workspaceId): ListWorkspaceBudgetsResponse;

    public function retrieveBudget(string $workspaceId, BudgetInterval|string $interval): WorkspaceBudgetResponse;

    /**
     * Creates or replaces the budget for one interval.
     */
    public function setBudget(
        string $workspaceId,
        BudgetInterval|string $interval,
        float $limitUsd,
        ?bool $includeByokInBudgets = null,
    ): WorkspaceBudgetResponse;

    public function deleteBudget(string $workspaceId, BudgetInterval|string $interval): DeleteWorkspaceBudgetResponse;

    public function listMembers(string $workspaceId, ?int $limit = null, ?int $offset = null): ListWorkspaceMembersResponse;

    /**
     * @param  list<string>  $userIds
     */
    public function addMembers(string $workspaceId, array $userIds): AddWorkspaceMembersResponse;

    /**
     * @param  list<string>  $userIds
     */
    public function removeMembers(string $workspaceId, array $userIds): RemoveWorkspaceMembersResponse;
}
