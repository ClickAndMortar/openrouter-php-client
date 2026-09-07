<?php

declare(strict_types=1);

namespace OpenRouter\Contracts\Resources;

use OpenRouter\Enums\Workspaces\WorkspaceRole;
use OpenRouter\Responses\Scim\DeleteScimGroupMappingResponse;
use OpenRouter\Responses\Scim\ListScimGroupMappingsResponse;
use OpenRouter\Responses\Scim\ListScimGroupsResponse;
use OpenRouter\Responses\Scim\ScimGroupMappingResponse;
use OpenRouter\Responses\Scim\ScimSyncJobResponse;

interface ScimContract
{
    /**
     * Groups synchronised from your identity provider. Read-only: membership
     * is managed upstream in the IdP.
     */
    public function listGroups(?int $limit = null, ?int $offset = null): ListScimGroupsResponse;

    public function listGroupMappings(?int $limit = null, ?int $offset = null): ListScimGroupMappingsResponse;

    public function createGroupMapping(
        string $scimGroupId,
        string $workspaceId,
        WorkspaceRole|string $role,
    ): ScimGroupMappingResponse;

    public function retrieveGroupMapping(string $id): ScimGroupMappingResponse;

    public function updateGroupMapping(string $id, WorkspaceRole|string $role): ScimGroupMappingResponse;

    /**
     * Removes a mapping. `$keepMembers` decides whether users provisioned
     * through it stay in the workspace; the API requires the choice to be
     * explicit, so it is always sent.
     */
    public function deleteGroupMapping(string $id, bool $keepMembers = false): DeleteScimGroupMappingResponse;

    /**
     * Starts a SCIM directory sync and returns the queued job. Management key
     * required.
     */
    public function createSyncJob(): ScimSyncJobResponse;

    /**
     * Reads the current state of a sync job. Management key required.
     */
    public function retrieveSyncJob(string $id): ScimSyncJobResponse;
}
