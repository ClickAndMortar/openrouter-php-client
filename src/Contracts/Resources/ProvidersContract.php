<?php

declare(strict_types=1);

namespace OpenRouter\Contracts\Resources;

use OpenRouter\Responses\Providers\ListProvidersResponse;

interface ProvidersContract
{
    /**
     * Lists all providers known to OpenRouter along with their metadata
     * (headquarters, datacenter locations, policy URLs).
     *
     * @see https://openrouter.ai/docs/api-reference/list-providers
     */
    public function list(): ListProvidersResponse;
}
