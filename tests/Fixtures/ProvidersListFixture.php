<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class ProvidersListFixture
{
    /**
     * Mirrors the `ProvidersListResponse` example from openapi-openrouter.yaml
     * for the `/providers` endpoint.
     *
     * @var array{data: array<int, array<string, mixed>>}
     */
    public const ATTRIBUTES = [
        'data' => [
            [
                'slug' => 'openai',
                'name' => 'OpenAI',
                'headquarters' => 'US',
                'datacenters' => ['US', 'IE'],
                'privacy_policy_url' => 'https://openai.com/privacy',
                'terms_of_service_url' => 'https://openai.com/terms',
                'status_page_url' => 'https://status.openai.com',
            ],
        ],
    ];
}
