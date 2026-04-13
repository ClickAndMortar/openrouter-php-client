<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class KeysCurrentFixture
{
    /**
     * Mirrors the 200 example for `GET /key`.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        'data' => [
            'byok_usage' => 17.38,
            'byok_usage_daily' => 17.38,
            'byok_usage_monthly' => 17.38,
            'byok_usage_weekly' => 17.38,
            'creator_user_id' => 'user_2dHFtVWx2n56w6HkM0000000000',
            'expires_at' => '2027-12-31T23:59:59Z',
            'include_byok_in_limit' => false,
            'is_free_tier' => false,
            'is_management_key' => false,
            'is_provisioning_key' => false,
            'label' => 'sk-or-v1-au7...890',
            'limit' => 100.0,
            'limit_remaining' => 74.5,
            'limit_reset' => 'monthly',
            'rate_limit' => [
                'interval' => '1h',
                'note' => 'This field is deprecated and safe to ignore.',
                'requests' => 1000,
            ],
            'usage' => 25.5,
            'usage_daily' => 25.5,
            'usage_monthly' => 25.5,
            'usage_weekly' => 25.5,
        ],
    ];
}
