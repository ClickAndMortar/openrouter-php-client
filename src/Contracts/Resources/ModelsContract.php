<?php

declare(strict_types=1);

namespace OpenRouter\Contracts\Resources;

use OpenRouter\Responses\Models\CountResponse;
use OpenRouter\Responses\Models\ListEndpointsResponse;
use OpenRouter\Responses\Models\ListResponse;

interface ModelsContract
{
    /**
     * Lists all available models and their properties.
     *
     * @see https://openrouter.ai/docs/api-reference/models#list-available-models
     */
    public function list(?string $category = null, ?string $supportedParameters = null, ?string $outputModalities = null): ListResponse;

    /**
     * Lists models filtered by user provider preferences, privacy settings, and guardrails.
     *
     * @see https://openrouter.ai/docs/api-reference/models#list-models-filtered-by-user-provider-preferences
     */
    public function listForUser(): ListResponse;

    /**
     * Returns the total count of available models.
     */
    public function count(?string $outputModalities = null): CountResponse;

    /**
     * Lists all endpoints for a given model.
     */
    public function listEndpoints(string $author, string $slug): ListEndpointsResponse;
}
