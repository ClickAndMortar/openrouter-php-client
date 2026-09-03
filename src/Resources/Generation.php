<?php

declare(strict_types=1);

namespace OpenRouter\Resources;

use OpenRouter\Contracts\Resources\GenerationContract;
use OpenRouter\Enums\Generation\FeedbackCategory;
use OpenRouter\Responses\DataResponse;
use OpenRouter\Responses\Generation\RetrieveGenerationResponse;
use OpenRouter\ValueObjects\Transporter\Payload;

final class Generation implements GenerationContract
{
    use Concerns\Transportable;

    /**
     * Retrieves request metadata for a previously-issued generation by its ID.
     *
     * @see https://openrouter.ai/docs/api-reference/get-a-generation
     */
    public function retrieve(string $id): RetrieveGenerationResponse
    {
        $payload = Payload::list('generation', ['id' => $id]);

        $response = $this->transporter->requestObject($payload);

        /** @var array{data: array<string, mixed>} $data */
        $data = $response->data();

        /** @phpstan-ignore-next-line — RetrieveGenerationResponse::from validates the shape at runtime */
        return RetrieveGenerationResponse::from($data, $response->meta());
    }

    /**
     * Retrieves the stored prompt, completion and error content for a
     * generation. Only available when the account or workspace has logging
     * enabled; the payload shape is not fixed by the spec.
     */
    public function content(string $id): DataResponse
    {
        $response = $this->transporter->requestObject(Payload::list('generation/content', ['id' => $id]));

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return DataResponse::from($data, $response->meta());
    }

    /**
     * Reports a problem with a generation.
     */
    public function submitFeedback(
        string $generationId,
        FeedbackCategory|string $category,
        ?string $comment = null,
    ): DataResponse {
        $body = [
            'generation_id' => $generationId,
            'category' => $category instanceof FeedbackCategory ? $category->value : $category,
        ];

        if ($comment !== null) {
            $body['comment'] = $comment;
        }

        $response = $this->transporter->requestObject(Payload::create('generation/feedback', $body));

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return DataResponse::from($data, $response->meta());
    }
}
