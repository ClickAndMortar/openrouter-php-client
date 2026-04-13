<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class KeysListFixture
{
    /**
     * Mirrors the 200 example for `GET /keys`.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        'data' => [
            [
                'byok_usage' => 17.38,
                'byok_usage_daily' => 17.38,
                'byok_usage_monthly' => 17.38,
                'byok_usage_weekly' => 17.38,
                'created_at' => '2025-08-24T10:30:00Z',
                'creator_user_id' => 'user_2dHFtVWx2n56w6HkM0000000000',
                'disabled' => false,
                'expires_at' => '2027-12-31T23:59:59Z',
                'hash' => 'f01d52606dc8f0a8303a7b5cc3fa07109c2e346cec7c0a16b40de462992ce943',
                'include_byok_in_limit' => false,
                'label' => 'Production API Key',
                'limit' => 100.0,
                'limit_remaining' => 74.5,
                'limit_reset' => 'monthly',
                'name' => 'My Production Key',
                'updated_at' => '2025-08-24T15:45:00Z',
                'usage' => 25.5,
                'usage_daily' => 25.5,
                'usage_monthly' => 25.5,
                'usage_weekly' => 25.5,
            ],
        ],
    ];
}
