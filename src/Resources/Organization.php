<?php

declare(strict_types=1);

namespace OpenRouter\Resources;

use OpenRouter\Contracts\Resources\OrganizationContract;
use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\Responses\Organization\ListMembersResponse;
use OpenRouter\ValueObjects\Transporter\Payload;

final class Organization implements OrganizationContract
{
    use Concerns\Transportable;

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
