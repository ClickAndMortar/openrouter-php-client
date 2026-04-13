<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Fixtures;

final class KeysCreateFixture
{
    /**
     * Mirrors the 201 example for `POST /keys`.
     *
     * @var array<string, mixed>
     */
    public const ATTRIBUTES = [
        'data' => [
            'byok_usage' => 0.0,
            'byok_usage_daily' => 0.0,
            'byok_usage_monthly' => 0.0,
            'byok_usage_weekly' => 0.0,
            'created_at' => '2025-08-24T10:30:00Z',
            'creator_user_id' => 'user_2dHFtVWx2n56w6HkM0000000000',
            'disabled' => false,
            'expires_at' => '2027-12-31T23:59:59Z',
            'hash' => 'f01d52606dc8f0a8303a7b5cc3fa07109c2e346cec7c0a16b40de462992ce943',
            'include_byok_in_limit' => true,
            'label' => 'My New API Key',
            'limit' => 50.0,
            'limit_remaining' => 50.0,
            'limit_reset' => 'monthly',
            'name' => 'My New API Key',
            'updated_at' => null,
            'usage' => 0.0,
            'usage_daily' => 0.0,
            'usage_monthly' => 0.0,
            'usage_weekly' => 0.0,
        ],
        'key' => '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',
    ];
}
