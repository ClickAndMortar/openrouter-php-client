<?php

declare(strict_types=1);

namespace OpenRouter\Contracts\Resources;

use OpenRouter\Responses\Activity\ListActivityResponse;

interface ActivityContract
{
    /**
     * Returns user activity data grouped by endpoint for the last 30 (completed) UTC days.
     * Requires a management key.
     *
     * @see https://openrouter.ai/docs/api-reference/get-user-activity
     */
    public function list(?string $date = null, ?string $apiKeyHash = null, ?string $userId = null): ListActivityResponse;
}
