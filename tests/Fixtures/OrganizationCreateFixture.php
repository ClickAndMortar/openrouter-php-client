<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class OrganizationCreateFixture
{
    /**
     * Mirrors the 200 example for `POST /organization`.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        'created' => true,
        'organization' => [
            'id' => 'org_01HQ8Z3K4M5N6P7Q8R9S',
            'name' => '[Parent] Acme Corp',
            'slug' => 'parent-acme-corp',
            'email' => 'owner@acme.example',
        ],
        'grant' => [
            'id' => 'grant_01HQ8Z3K4M5N6P7Q8R9S',
            'scopes' => ['inference', 'workspaces_read', 'keys_read'],
        ],
        'management_key' => [
            'name' => 'Acme Corp management key',
            'key' => 'sk-or-v1-management-key-returned-once',
        ],
    ];
}
