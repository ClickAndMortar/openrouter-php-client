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
     * `$filters` accepts any additional query parameter documented for
     * `GET /models` - pagination (`limit`, `offset`), free-text `q`, `sort`,
     * `input_modalities`, `context`, `arch`, `model_authors`, `providers`,
     * `distillable`, `zdr`, `region`, and the `min_*`/`max_*` bounds for
     * `price`, `output_price`, `age_days`, `intelligence_index`, `coding_index`,
     * `agentic_index` and `tool_success_rate`. Null values are dropped.
     *
     * @param  array<string, scalar|null>  $filters
     *
     * @see https://openrouter.ai/docs/api-reference/models#list-available-models
     */
    public function list(
        ?string $category = null,
        ?string $supportedParameters = null,
        ?string $outputModalities = null,
        array $filters = [],
    ): ListResponse;

    /**
     * Lists models filtered by user provider preferences, privacy settings, and guardrails.
     *
     * @see https://openrouter.ai/docs/api-reference/models#list-models-filtered-by-user-provider-preferences
     */
    public function listForUser(
        ?int $limit = null,
        ?int $offset = null,
        ?string $outputModalities = null,
    ): ListResponse;

    /**
     * Returns the total count of available models.
     */
    public function count(?string $outputModalities = null): CountResponse;

    /**
     * Lists all endpoints for a given model.
     */
    public function listEndpoints(string $author, string $slug): ListEndpointsResponse;
}
