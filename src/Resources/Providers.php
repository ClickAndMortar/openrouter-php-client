<?php

declare(strict_types=1);

namespace OpenRouter\Resources;

use OpenRouter\Contracts\Resources\ProvidersContract;
use OpenRouter\Responses\Providers\ListProvidersResponse;
use OpenRouter\ValueObjects\Transporter\Payload;

final class Providers implements ProvidersContract
{
    use Concerns\Transportable;

    /**
     * Lists all providers known to OpenRouter along with their metadata
     * (headquarters, datacenter locations, policy URLs).
     *
     * @see https://openrouter.ai/docs/api-reference/list-providers
     */
    public function list(): ListProvidersResponse
    {
        $payload = Payload::list('providers');

        $response = $this->transporter->requestObject($payload);

        /** @var array{data: array<int, array<string, mixed>>} $data */
        $data = $response->data();

        /** @phpstan-ignore-next-line — ListProvidersResponse::from validates the shape at runtime */
        return ListProvidersResponse::from($data, $response->meta());
    }
}
