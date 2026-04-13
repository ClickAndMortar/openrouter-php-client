<?php

declare(strict_types=1);

namespace OpenRouter\Contracts\Resources;

use OpenRouter\Responses\Models\ListResponse;

interface ModelsContract
{
    /**
     * Lists models filtered by user provider preferences, privacy settings, and guardrails.
     *
     * @see https://openrouter.ai/docs/api-reference/models#list-models-filtered-by-user-provider-preferences
     */
    public function listForUser(): ListResponse;
}
