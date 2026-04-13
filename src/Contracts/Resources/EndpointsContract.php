<?php

declare(strict_types=1);

namespace OpenRouter\Contracts\Resources;

use OpenRouter\Responses\Endpoints\ListZdrEndpointsResponse;

interface EndpointsContract
{
    /**
     * Preview the impact of ZDR (Zero Data Retention) on the available endpoints.
     *
     * @see https://openrouter.ai/docs/api-reference/list-endpoints-zdr
     */
    public function listZdr(): ListZdrEndpointsResponse;
}
