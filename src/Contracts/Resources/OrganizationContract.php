<?php

declare(strict_types=1);

namespace OpenRouter\Contracts\Resources;

use OpenRouter\Responses\Organization\CreateOrganizationResponse;
use OpenRouter\Responses\Organization\ListMembersResponse;

interface OrganizationContract
{
    /**
     * Creates a customer organization via `POST /organization`. Requires a
     * management key from a Connect-enabled organization. Idempotent per
     * customer; the plaintext management key is returned only once.
     */
    public function create(string $name, string $email): CreateOrganizationResponse;

    /**
     * Lists all members of the organization associated with the authenticated
     * management key via `GET /organization/members`. Management key required.
     *
     * @see https://openrouter.ai/docs/api-reference/list-organization-members
     */
    public function listMembers(?int $offset = null, ?int $limit = null): ListMembersResponse;
}
