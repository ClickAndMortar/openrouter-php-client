<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class OrganizationMembersFixture
{
    /**
     * Mirrors the 200 example for `GET /organization/members`.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        'data' => [
            [
                'id' => 'user_2dHFtVWx2n56w6HkM0000000000',
                'email' => 'jane.doe@example.com',
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'role' => 'org:member',
            ],
            [
                'id' => 'user_adminXXX0000000000',
                'email' => 'admin@example.com',
                'first_name' => null,
                'last_name' => null,
                'role' => 'org:admin',
            ],
        ],
        'total_count' => 25,
    ];
}
