<?php

declare(strict_types=1);

namespace OpenRouter\Contracts\Resources;

use OpenRouter\Responses\Benchmarks\ListBenchmarksResponse;
use OpenRouter\Responses\DataResponse;

interface BenchmarksContract
{
    /**
     * @param  array<string, scalar|null>  $filters  `source`, `benchmark_type`,
     *                                               `include_run_config`,
     *                                               `search_engine`,
     *                                               `search_surface`, `arena`,
     *                                               `category`
     */
    public function list(
        ?string $taskType = null,
        ?int $maxResults = null,
        array $filters = [],
    ): ListBenchmarksResponse;

    /**
     * Market share by task classification. `$window` selects the reporting
     * window (for example `30d`).
     */
    public function taskClassification(?string $window = null): DataResponse;
}
