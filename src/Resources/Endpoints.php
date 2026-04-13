<?php

declare(strict_types=1);

namespace OpenRouter\Resources;

use OpenRouter\Contracts\Resources\EndpointsContract;
use OpenRouter\Responses\Endpoints\ListZdrEndpointsResponse;
use OpenRouter\ValueObjects\Transporter\Payload;

final class Endpoints implements EndpointsContract
{
    use Concerns\Transportable;

    /**
     * Preview the impact of ZDR (Zero Data Retention) on the available endpoints.
     *
     * @see https://openrouter.ai/docs/api-reference/list-endpoints-zdr
     */
    public function listZdr(): ListZdrEndpointsResponse
    {
        $payload = Payload::list('endpoints/zdr');

        $response = $this->transporter->requestObject($payload);

        /** @var array{data: array<int, array<string, mixed>>} $data */
        $data = $response->data();

        /** @phpstan-ignore-next-line — ListZdrEndpointsResponse::from validates the shape at runtime */
        return ListZdrEndpointsResponse::from($data, $response->meta());
    }
}
