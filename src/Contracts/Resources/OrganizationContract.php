<?php

declare(strict_types=1);

namespace OpenRouter\Contracts\Resources;

use OpenRouter\Responses\Organization\ListMembersResponse;

interface OrganizationContract
{
    /**
     * Lists all members of the organization associated with the authenticated
     * management key via `GET /organization/members`. Management key required.
     *
     * @see https://openrouter.ai/docs/api-reference/list-organization-members
     */
    public function listMembers(?int $offset = null, ?int $limit = null): ListMembersResponse;
}
