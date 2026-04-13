<?php

declare(strict_types=1);

namespace OpenRouter\Resources;

use OpenRouter\Contracts\Resources\ActivityContract;
use OpenRouter\Responses\Activity\ListActivityResponse;
use OpenRouter\ValueObjects\Transporter\Payload;

final class Activity implements ActivityContract
{
    use Concerns\Transportable;

    /**
     * Returns user activity data grouped by endpoint for the last 30 (completed) UTC days.
     * Requires a management key.
     *
     * @see https://openrouter.ai/docs/api-reference/get-user-activity
     */
    public function list(?string $date = null, ?string $apiKeyHash = null, ?string $userId = null): ListActivityResponse
    {
        $query = array_filter(
            [
                'date' => $date,
                'api_key_hash' => $apiKeyHash,
                'user_id' => $userId,
            ],
            static fn (?string $value): bool => $value !== null,
        );

        $payload = Payload::list('activity', $query);

        $response = $this->transporter->requestObject($payload);

        /** @var array{data: array<int, array<string, mixed>>} $data */
        $data = $response->data();

        /** @phpstan-ignore-next-line — ListActivityResponse::from validates the shape at runtime */
        return ListActivityResponse::from($data, $response->meta());
    }
}
