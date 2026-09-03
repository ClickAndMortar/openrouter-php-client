<?php

declare(strict_types=1);

namespace OpenRouter\Resources;

use OpenRouter\Contracts\Resources\ScimContract;
use OpenRouter\Enums\Workspaces\WorkspaceRole;
use OpenRouter\Responses\Scim\DeleteScimGroupMappingResponse;
use OpenRouter\Responses\Scim\ListScimGroupMappingsResponse;
use OpenRouter\Responses\Scim\ListScimGroupsResponse;
use OpenRouter\Responses\Scim\ScimGroupMappingResponse;
use OpenRouter\ValueObjects\Transporter\Payload;

final class Scim implements ScimContract
{
    use Concerns\Paginates;
    use Concerns\Transportable;

    public function listGroups(?int $limit = null, ?int $offset = null): ListScimGroupsResponse
    {
        $response = $this->transporter->requestObject(
            Payload::list('scim/groups', self::page($limit, $offset)),
        );

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return ListScimGroupsResponse::from($data, $response->meta());
    }

    public function listGroupMappings(?int $limit = null, ?int $offset = null): ListScimGroupMappingsResponse
    {
        $response = $this->transporter->requestObject(
            Payload::list('scim/group-mappings', self::page($limit, $offset)),
        );

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return ListScimGroupMappingsResponse::from($data, $response->meta());
    }

    public function createGroupMapping(
        string $scimGroupId,
        string $workspaceId,
        WorkspaceRole|string $role,
    ): ScimGroupMappingResponse {
        return $this->mapping(Payload::create('scim/group-mappings', [
            'scim_group_id' => $scimGroupId,
            'workspace_id' => $workspaceId,
            'role' => self::role($role),
        ]));
    }

    public function retrieveGroupMapping(string $id): ScimGroupMappingResponse
    {
        return $this->mapping(Payload::retrieve('scim/group-mappings', $id));
    }

    public function updateGroupMapping(string $id, WorkspaceRole|string $role): ScimGroupMappingResponse
    {
        return $this->mapping(Payload::modify('scim/group-mappings', $id, ['role' => self::role($role)]));
    }

    public function deleteGroupMapping(string $id, bool $keepMembers = false): DeleteScimGroupMappingResponse
    {
        $payload = Payload::delete(
            'scim/group-mappings',
            $id,
            query: ['keep_members' => $keepMembers ? 'true' : 'false'],
        );

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return DeleteScimGroupMappingResponse::from($data, $response->meta());
    }

    private function mapping(Payload $payload): ScimGroupMappingResponse
    {
        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return ScimGroupMappingResponse::from($data, $response->meta());
    }

    private static function role(WorkspaceRole|string $role): string
    {
        return $role instanceof WorkspaceRole ? $role->value : $role;
    }
}
