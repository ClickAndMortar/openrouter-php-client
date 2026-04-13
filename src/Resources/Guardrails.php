<?php

declare(strict_types=1);

namespace OpenRouter\Resources;

use OpenRouter\Contracts\Resources\GuardrailsContract;
use OpenRouter\Exceptions\InvalidArgumentException;
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
use OpenRouter\ValueObjects\Transporter\Payload;

final class Guardrails implements GuardrailsContract
{
    use Concerns\Transportable;

    public function list(?int $offset = null, ?int $limit = null): ListGuardrailsResponse
    {
        $payload = Payload::list('guardrails', $this->paginationQuery('Guardrails::list', $offset, $limit));

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return ListGuardrailsResponse::from($data, $response->meta());
    }

    /**
     * @param  CreateGuardrailRequest|array<string, mixed>  $parameters
     */
    public function create(CreateGuardrailRequest|array $parameters): CreateGuardrailResponse
    {
        $params = $parameters instanceof CreateGuardrailRequest
            ? $parameters->toArray()
            : $parameters;

        $payload = Payload::create('guardrails', $params);

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return CreateGuardrailResponse::from($data, $response->meta());
    }

    public function retrieve(string $id): GetGuardrailResponse
    {
        $this->requireId('Guardrails::retrieve', $id);

        $payload = Payload::retrieve('guardrails', $id);

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return GetGuardrailResponse::from($data, $response->meta());
    }

    /**
     * @param  UpdateGuardrailRequest|array<string, mixed>  $parameters
     */
    public function update(string $id, UpdateGuardrailRequest|array $parameters): UpdateGuardrailResponse
    {
        $this->requireId('Guardrails::update', $id);

        $params = $parameters instanceof UpdateGuardrailRequest
            ? $parameters->toArray()
            : $parameters;

        $payload = Payload::modify('guardrails', $id, $params);

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return UpdateGuardrailResponse::from($data, $response->meta());
    }

    public function delete(string $id): DeleteGuardrailResponse
    {
        $this->requireId('Guardrails::delete', $id);

        $payload = Payload::delete('guardrails', $id);

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return DeleteGuardrailResponse::from($data, $response->meta());
    }

    public function listKeyAssignments(string $id, ?int $offset = null, ?int $limit = null): ListKeyAssignmentsResponse
    {
        $this->requireId('Guardrails::listKeyAssignments', $id);

        $payload = Payload::list(
            "guardrails/{$id}/assignments/keys",
            $this->paginationQuery('Guardrails::listKeyAssignments', $offset, $limit),
        );

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return ListKeyAssignmentsResponse::from($data, $response->meta());
    }

    /**
     * @param  list<string>  $keyHashes
     */
    public function bulkAssignKeys(string $id, array $keyHashes): BulkAssignKeysResponse
    {
        $this->requireId('Guardrails::bulkAssignKeys', $id);
        $this->requireNonEmptyStringList('Guardrails::bulkAssignKeys', 'keyHashes', $keyHashes);

        $payload = Payload::create(
            "guardrails/{$id}/assignments/keys",
            ['key_hashes' => array_values($keyHashes)],
        );

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return BulkAssignKeysResponse::from($data, $response->meta());
    }

    /**
     * @param  list<string>  $keyHashes
     */
    public function bulkUnassignKeys(string $id, array $keyHashes): BulkUnassignKeysResponse
    {
        $this->requireId('Guardrails::bulkUnassignKeys', $id);
        $this->requireNonEmptyStringList('Guardrails::bulkUnassignKeys', 'keyHashes', $keyHashes);

        $payload = Payload::create(
            "guardrails/{$id}/assignments/keys/remove",
            ['key_hashes' => array_values($keyHashes)],
        );

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return BulkUnassignKeysResponse::from($data, $response->meta());
    }

    public function listMemberAssignments(string $id, ?int $offset = null, ?int $limit = null): ListMemberAssignmentsResponse
    {
        $this->requireId('Guardrails::listMemberAssignments', $id);

        $payload = Payload::list(
            "guardrails/{$id}/assignments/members",
            $this->paginationQuery('Guardrails::listMemberAssignments', $offset, $limit),
        );

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return ListMemberAssignmentsResponse::from($data, $response->meta());
    }

    /**
     * @param  list<string>  $memberUserIds
     */
    public function bulkAssignMembers(string $id, array $memberUserIds): BulkAssignMembersResponse
    {
        $this->requireId('Guardrails::bulkAssignMembers', $id);
        $this->requireNonEmptyStringList('Guardrails::bulkAssignMembers', 'memberUserIds', $memberUserIds);

        $payload = Payload::create(
            "guardrails/{$id}/assignments/members",
            ['member_user_ids' => array_values($memberUserIds)],
        );

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return BulkAssignMembersResponse::from($data, $response->meta());
    }

    /**
     * @param  list<string>  $memberUserIds
     */
    public function bulkUnassignMembers(string $id, array $memberUserIds): BulkUnassignMembersResponse
    {
        $this->requireId('Guardrails::bulkUnassignMembers', $id);
        $this->requireNonEmptyStringList('Guardrails::bulkUnassignMembers', 'memberUserIds', $memberUserIds);

        $payload = Payload::create(
            "guardrails/{$id}/assignments/members/remove",
            ['member_user_ids' => array_values($memberUserIds)],
        );

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return BulkUnassignMembersResponse::from($data, $response->meta());
    }

    public function listAllKeyAssignments(?int $offset = null, ?int $limit = null): ListKeyAssignmentsResponse
    {
        $payload = Payload::list(
            'guardrails/assignments/keys',
            $this->paginationQuery('Guardrails::listAllKeyAssignments', $offset, $limit),
        );

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return ListKeyAssignmentsResponse::from($data, $response->meta());
    }

    public function listAllMemberAssignments(?int $offset = null, ?int $limit = null): ListMemberAssignmentsResponse
    {
        $payload = Payload::list(
            'guardrails/assignments/members',
            $this->paginationQuery('Guardrails::listAllMemberAssignments', $offset, $limit),
        );

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return ListMemberAssignmentsResponse::from($data, $response->meta());
    }

    /**
     * @return array<string, int>
     */
    private function paginationQuery(string $context, ?int $offset, ?int $limit): array
    {
        $query = [];
        if ($offset !== null) {
            if ($offset < 0) {
                throw new InvalidArgumentException("{$context}()::\$offset must be >= 0");
            }
            $query['offset'] = $offset;
        }
        if ($limit !== null) {
            if ($limit < 1 || $limit > 100) {
                throw new InvalidArgumentException("{$context}()::\$limit must be between 1 and 100");
            }
            $query['limit'] = $limit;
        }

        return $query;
    }

    private function requireId(string $context, string $id): void
    {
        if ($id === '') {
            throw new InvalidArgumentException("{$context}()::\$id must not be an empty string");
        }
    }

    /**
     * @param  list<string>  $values
     */
    private function requireNonEmptyStringList(string $context, string $paramName, array $values): void
    {
        if ($values === []) {
            throw new InvalidArgumentException("{$context}()::\${$paramName} must not be empty");
        }
        foreach ($values as $value) {
            if (! is_string($value) || $value === '') {
                throw new InvalidArgumentException("{$context}()::\${$paramName} must contain only non-empty strings");
            }
        }
    }
}
