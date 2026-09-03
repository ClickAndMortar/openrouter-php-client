<?php

declare(strict_types=1);

namespace OpenRouter\Resources;

use OpenRouter\Contracts\Resources\BenchmarksContract;
use OpenRouter\Responses\Benchmarks\ListBenchmarksResponse;
use OpenRouter\Responses\DataResponse;
use OpenRouter\ValueObjects\Transporter\Payload;

final class Benchmarks implements BenchmarksContract
{
    use Concerns\Transportable;

    /**
     * @param  array<string, scalar|null>  $filters
     */
    public function list(
        ?string $taskType = null,
        ?int $maxResults = null,
        array $filters = [],
    ): ListBenchmarksResponse {
        $query = array_filter(
            ['task_type' => $taskType, 'max_results' => $maxResults, ...$filters],
            static fn (mixed $value): bool => $value !== null,
        );

        $response = $this->transporter->requestObject(Payload::list('benchmarks', $query));

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return ListBenchmarksResponse::from($data, $response->meta());
    }

    public function taskClassification(?string $window = null): DataResponse
    {
        $query = $window === null ? [] : ['window' => $window];

        $response = $this->transporter->requestObject(Payload::list('classifications/task', $query));

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return DataResponse::from($data, $response->meta());
    }
}
