<?php

declare(strict_types=1);

namespace OpenRouter\Resources;

use OpenRouter\Contracts\Resources\AnalyticsContract;
use OpenRouter\Responses\DataResponse;
use OpenRouter\ValueObjects\Transporter\Payload;

final class Analytics implements AnalyticsContract
{
    use Concerns\Transportable;

    public function meta(): DataResponse
    {
        $response = $this->transporter->requestObject(Payload::list('analytics/meta'));

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return DataResponse::from($data, $response->meta());
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public function query(array $parameters): DataResponse
    {
        $response = $this->transporter->requestObject(Payload::create('analytics/query', $parameters));

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return DataResponse::from($data, $response->meta());
    }
}
