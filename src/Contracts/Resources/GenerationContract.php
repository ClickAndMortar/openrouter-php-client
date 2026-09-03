<?php

declare(strict_types=1);

namespace OpenRouter\Contracts\Resources;

use OpenRouter\Enums\Generation\FeedbackCategory;
use OpenRouter\Responses\DataResponse;
use OpenRouter\Responses\Generation\RetrieveGenerationResponse;

interface GenerationContract
{
    /**
     * Retrieves request metadata for a previously-issued generation by its ID.
     *
     * @see https://openrouter.ai/docs/api-reference/get-a-generation
     */
    public function retrieve(string $id): RetrieveGenerationResponse;

    /**
     * Stored prompt, completion and error content for a generation, when
     * logging is enabled.
     */
    public function content(string $id): DataResponse;

    /**
     * Reports a problem with a generation.
     */
    public function submitFeedback(
        string $generationId,
        FeedbackCategory|string $category,
        ?string $comment = null,
    ): DataResponse;
}
