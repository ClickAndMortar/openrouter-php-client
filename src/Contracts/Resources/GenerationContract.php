<?php

declare(strict_types=1);

namespace OpenRouter\Contracts\Resources;

use OpenRouter\Responses\Generation\RetrieveGenerationResponse;

interface GenerationContract
{
    /**
     * Retrieves request metadata for a previously-issued generation by its ID.
     *
     * @see https://openrouter.ai/docs/api-reference/get-a-generation
     */
    public function retrieve(string $id): RetrieveGenerationResponse;
}
