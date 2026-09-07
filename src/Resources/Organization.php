<?php

declare(strict_types=1);

namespace OpenRouter\Resources;

use OpenRouter\Contracts\Resources\OrganizationContract;
use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\Responses\Organization\CreateOrganizationResponse;
use OpenRouter\Responses\Organization\ListMembersResponse;
use OpenRouter\ValueObjects\Transporter\Payload;

final class Organization implements OrganizationContract
{
    use Concerns\Transportable;

    /**
     * Creates a customer organization owned by a managed user. Requires a
     * management key belonging to a Connect-enabled organization.
     *
     * The organization is created unfunded — fund it before running inference.
     * A repeat call for the same customer returns the existing organization
     * with `created` false, and a management key only when none was ever
     * successfully delivered.
     */
    public function create(string $name, string $email): CreateOrganizationResponse
    {
        if (trim($name) === '') {
            throw new InvalidArgumentException('Organization::create()::$name must not be empty');
        }

        if (trim($email) === '') {
            throw new InvalidArgumentException('Organization::create()::$email must not be empty');
        }

        $response = $this->transporter->requestObject(
            Payload::create('organization', ['name' => $name, 'email' => $email]),
        );

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return CreateOrganizationResponse::from($data, $response->meta());
    }

    public function listMembers(?int $offset = null, ?int $limit = null): ListMembersResponse
    {
        $query = [];

        if ($offset !== null) {
            if ($offset < 0) {
                throw new InvalidArgumentException('Organization::listMembers()::$offset must be >= 0');
            }
            $query['offset'] = $offset;
        }

        if ($limit !== null) {
            if ($limit < 1 || $limit > 100) {
                throw new InvalidArgumentException('Organization::listMembers()::$limit must be between 1 and 100');
            }
            $query['limit'] = $limit;
        }

        $payload = Payload::list('organization/members', $query);

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return ListMembersResponse::from($data, $response->meta());
    }
}
