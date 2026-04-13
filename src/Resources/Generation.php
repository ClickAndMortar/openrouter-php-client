<?php

declare(strict_types=1);

namespace OpenRouter\Resources;

use OpenRouter\Contracts\Resources\GenerationContract;
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
}
