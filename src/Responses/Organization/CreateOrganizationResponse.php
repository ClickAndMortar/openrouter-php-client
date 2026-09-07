<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Organization;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * Result of `POST /organization`.
 *
 * `$created` distinguishes a fresh organization from an idempotent replay for
 * the same customer. `$managementKey` is null on a replay whose key was already
 * delivered — see {@see OrganizationManagementKey}.
 *
 * @phpstan-type CreateOrganizationResponseType array<string, mixed>
 *
 * @implements ResponseContract<CreateOrganizationResponseType>
 */
final class CreateOrganizationResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<CreateOrganizationResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    /**
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly bool $created,
        public readonly CreatedOrganization $organization,
        public readonly OrganizationGrant $grant,
        public readonly ?OrganizationManagementKey $managementKey,
        public readonly array $extras,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  CreateOrganizationResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $key = $attributes['management_key'] ?? null;

        return new self(
            created: (bool) ($attributes['created'] ?? false),
            organization: CreatedOrganization::from(
                is_array($attributes['organization'] ?? null) ? $attributes['organization'] : [],
            ),
            grant: OrganizationGrant::from(
                is_array($attributes['grant'] ?? null) ? $attributes['grant'] : [],
            ),
            managementKey: is_array($key) ? OrganizationManagementKey::from($key) : null,
            extras: array_diff_key(
                $attributes,
                array_flip(['created', 'organization', 'grant', 'management_key']),
            ),
            meta: $meta,
        );
    }

    /**
     * @return CreateOrganizationResponseType
     */
    public function toArray(): array
    {
        return [
            'created' => $this->created,
            'organization' => $this->organization->toArray(),
            'grant' => $this->grant->toArray(),
            'management_key' => $this->managementKey?->toArray(),
            ...$this->extras,
        ];
    }
}
