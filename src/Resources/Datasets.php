<?php

declare(strict_types=1);

namespace OpenRouter\Resources;

use OpenRouter\Contracts\Resources\DatasetsContract;
use OpenRouter\Responses\Datasets\AppRankingsResponse;
use OpenRouter\Responses\Datasets\DailyRankingsResponse;
use OpenRouter\Responses\Datasets\SessionCostResponse;
use OpenRouter\ValueObjects\Transporter\Payload;

final class Datasets implements DatasetsContract
{
    use Concerns\Transportable;

    /**
     * @param  array<string, scalar|null>  $filters
     */
    public function appRankings(
        ?string $category = null,
        ?string $startDate = null,
        ?string $endDate = null,
        ?int $limit = null,
        ?int $offset = null,
        array $filters = [],
    ): AppRankingsResponse {
        $payload = Payload::list('datasets/app-rankings', self::query([
            'category' => $category,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'limit' => $limit,
            'offset' => $offset,
            ...$filters,
        ]));

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return AppRankingsResponse::from($data, $response->meta());
    }

    /**
     * @param  array<string, scalar|null>  $filters
     */
    public function dailyRankings(
        ?string $startDate = null,
        ?string $endDate = null,
        array $filters = [],
    ): DailyRankingsResponse {
        $payload = Payload::list('datasets/rankings-daily', self::query([
            'start_date' => $startDate,
            'end_date' => $endDate,
            ...$filters,
        ]));

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return DailyRankingsResponse::from($data, $response->meta());
    }

    /**
     * @param  array<string, scalar|null>  $filters
     */
    public function sessionCost(
        ?string $appSlug = null,
        ?string $model = null,
        ?int $limit = null,
        ?int $offset = null,
        array $filters = [],
    ): SessionCostResponse {
        $payload = Payload::list('datasets/session-cost', self::query([
            'app_slug' => $appSlug,
            'model' => $model,
            'limit' => $limit,
            'offset' => $offset,
            ...$filters,
        ]));

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return SessionCostResponse::from($data, $response->meta());
    }

    /**
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    private static function query(array $params): array
    {
        return array_filter($params, static fn (mixed $value): bool => $value !== null);
    }
}
