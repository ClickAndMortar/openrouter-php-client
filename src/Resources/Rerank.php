<?php

declare(strict_types=1);

namespace OpenRouter\Resources;

use OpenRouter\Contracts\Resources\RerankContract;
use OpenRouter\Responses\Rerank\RerankResponse;
use OpenRouter\ValueObjects\Rerank\RerankRequest;
use OpenRouter\ValueObjects\Transporter\Payload;

final class Rerank implements RerankContract
{
    use Concerns\Transportable;

    /**
     * @param  RerankRequest|array<string, mixed>  $parameters
     */
    public function rerank(RerankRequest|array $parameters): RerankResponse
    {
        $params = $parameters instanceof RerankRequest
            ? $parameters->toArray()
            : $parameters;

        $payload = Payload::create('rerank', $params);

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return RerankResponse::from($data, $response->meta());
    }
}
