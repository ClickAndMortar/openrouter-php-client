<?php

declare(strict_types=1);

namespace OpenRouter\Contracts\Resources;

use OpenRouter\Responses\DataResponse;

interface AnalyticsContract
{
    /**
     * The metrics, dimensions and granularities available to {@see query()}.
     * Call it first to discover valid identifiers rather than hardcoding them.
     */
    public function meta(): DataResponse;

    /**
     * Runs an analytics query. `metrics` is required; `dimensions`, `filters`,
     * `granularity`, `time_range`, `order_by`, `limit` and `group_limit` are
     * optional. The result shape follows the query, so it is returned as a
     * raw array.
     *
     * @param  array<string, mixed>  $parameters
     */
    public function query(array $parameters): DataResponse;
}
