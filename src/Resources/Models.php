<?php

declare(strict_types=1);

namespace OpenRouter\Resources;

use OpenRouter\Contracts\Resources\ModelsContract;
use OpenRouter\Responses\Models\ListResponse;
use OpenRouter\ValueObjects\Transporter\Payload;

final class Models implements ModelsContract
{
    use Concerns\Transportable;

    /**
     * Lists models filtered by user provider preferences, privacy settings, and guardrails.
     *
     * @see https://openrouter.ai/docs/api-reference/models#list-models-filtered-by-user-provider-preferences
     */
    public function listForUser(): ListResponse
    {
        $payload = Payload::list('models/user');

        $response = $this->transporter->requestObject($payload);

        /** @var array{data: array<int, array<string, mixed>>} $data */
        $data = $response->data();

        /** @phpstan-ignore-next-line — ListResponse::from validates the shape at runtime */
        return ListResponse::from($data, $response->meta());
    }
}
