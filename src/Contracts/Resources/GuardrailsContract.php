<?php

declare(strict_types=1);

namespace OpenRouter\Contracts\Resources;

use OpenRouter\Responses\Guardrails\BulkAssignKeysResponse;
use OpenRouter\Responses\Guardrails\BulkAssignMembersResponse;
use OpenRouter\Responses\Guardrails\BulkUnassignKeysResponse;
use OpenRouter\Responses\Guardrails\BulkUnassignMembersResponse;
use OpenRouter\Responses\Guardrails\CreateGuardrailResponse;
use OpenRouter\Responses\Guardrails\DeleteGuardrailResponse;
use OpenRouter\Responses\Guardrails\GetGuardrailResponse;
use OpenRouter\Responses\Guardrails\ListGuardrailsResponse;
use OpenRouter\Responses\Guardrails\ListKeyAssignmentsResponse;
use OpenRouter\Responses\Guardrails\ListMemberAssignmentsResponse;
use OpenRouter\Responses\Guardrails\UpdateGuardrailResponse;
use OpenRouter\ValueObjects\Guardrails\CreateGuardrailRequest;
use OpenRouter\ValueObjects\Guardrails\UpdateGuardrailRequest;

interface GuardrailsContract
{
    /**
     * Lists all guardrails for the authenticated user via `GET /guardrails`.
     * Management key required.
     */
    public function list(?int $offset = null, ?int $limit = null): ListGuardrailsResponse;

    /**
     * Creates a new guardrail via `POST /guardrails`. Management key required.
     *
     * @param  CreateGuardrailRequest|array<string, mixed>  $parameters
     */
    public function create(CreateGuardrailRequest|array $parameters): CreateGuardrailResponse;

    /**
     * Retrieves a single guardrail by ID via `GET /guardrails/{id}`.
     * Management key required.
     */
    public function retrieve(string $id): GetGuardrailResponse;

    /**
     * Updates a guardrail by ID via `PATCH /guardrails/{id}`.
     * Management key required.
     *
     * @param  UpdateGuardrailRequest|array<string, mixed>  $parameters
     */
    public function update(string $id, UpdateGuardrailRequest|array $parameters): UpdateGuardrailResponse;

    /**
     * Deletes a guardrail by ID via `DELETE /guardrails/{id}`.
     * Management key required.
     */
    public function delete(string $id): DeleteGuardrailResponse;

    /**
     * Lists key assignments for a specific guardrail via
     * `GET /guardrails/{id}/assignments/keys`. Management key required.
     */
    public function listKeyAssignments(string $id, ?int $offset = null, ?int $limit = null): ListKeyAssignmentsResponse;

    /**
     * Bulk-assigns API keys to a guardrail via
     * `POST /guardrails/{id}/assignments/keys`. Management key required.
     *
     * @param  list<string>  $keyHashes
     */
    public function bulkAssignKeys(string $id, array $keyHashes): BulkAssignKeysResponse;

    /**
     * Bulk-unassigns API keys from a guardrail via
     * `POST /guardrails/{id}/assignments/keys/remove`. Management key required.
     *
     * @param  list<string>  $keyHashes
     */
    public function bulkUnassignKeys(string $id, array $keyHashes): BulkUnassignKeysResponse;

    /**
     * Lists organization member assignments for a specific guardrail via
     * `GET /guardrails/{id}/assignments/members`. Management key required.
     */
    public function listMemberAssignments(string $id, ?int $offset = null, ?int $limit = null): ListMemberAssignmentsResponse;

    /**
     * Bulk-assigns organization members to a guardrail via
     * `POST /guardrails/{id}/assignments/members`. Management key required.
     *
     * @param  list<string>  $memberUserIds
     */
    public function bulkAssignMembers(string $id, array $memberUserIds): BulkAssignMembersResponse;

    /**
     * Bulk-unassigns organization members from a guardrail via
     * `POST /guardrails/{id}/assignments/members/remove`. Management key required.
     *
     * @param  list<string>  $memberUserIds
     */
    public function bulkUnassignMembers(string $id, array $memberUserIds): BulkUnassignMembersResponse;

    /**
     * Lists all API key guardrail assignments for the authenticated user via
     * `GET /guardrails/assignments/keys`. Management key required.
     */
    public function listAllKeyAssignments(?int $offset = null, ?int $limit = null): ListKeyAssignmentsResponse;

    /**
     * Lists all organization member guardrail assignments for the authenticated
     * user via `GET /guardrails/assignments/members`. Management key required.
     */
    public function listAllMemberAssignments(?int $offset = null, ?int $limit = null): ListMemberAssignmentsResponse;
}
