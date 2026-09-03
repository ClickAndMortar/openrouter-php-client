<?php

declare(strict_types=1);

namespace OpenRouter\Contracts\Resources;

use OpenRouter\Responses\Datasets\AppRankingsResponse;
use OpenRouter\Responses\Datasets\DailyRankingsResponse;
use OpenRouter\Responses\Datasets\SessionCostResponse;

/**
 * OpenRouter's public usage datasets. Read-only and aggregate — they describe
 * the platform as a whole, not your own account.
 */
interface DatasetsContract
{
    /**
     * @param  array<string, scalar|null>  $filters  `subcategory`, `sort`
     */
    public function appRankings(
        ?string $category = null,
        ?string $startDate = null,
        ?string $endDate = null,
        ?int $limit = null,
        ?int $offset = null,
        array $filters = [],
    ): AppRankingsResponse;

    /**
     * @param  array<string, scalar|null>  $filters  `period`, `modality`,
     *                                               `context_bucket`, `category`,
     *                                               `language_type`
     */
    public function dailyRankings(
        ?string $startDate = null,
        ?string $endDate = null,
        array $filters = [],
    ): DailyRankingsResponse;

    /**
     * @param  array<string, scalar|null>  $filters  `turn_range`
     */
    public function sessionCost(
        ?string $appSlug = null,
        ?string $model = null,
        ?int $limit = null,
        ?int $offset = null,
        array $filters = [],
    ): SessionCostResponse;
}
