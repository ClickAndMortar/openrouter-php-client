<?php

declare(strict_types=1);

namespace OpenRouter\Resources;

use OpenRouter\Contracts\Resources\ObservabilityContract;
use OpenRouter\Responses\Observability\DeleteObservabilityDestinationResponse;
use OpenRouter\Responses\Observability\ListObservabilityDestinationsResponse;
use OpenRouter\Responses\Observability\ObservabilityDestinationResponse;
use OpenRouter\ValueObjects\Observability\CreateObservabilityDestinationRequest;
use OpenRouter\ValueObjects\Observability\UpdateObservabilityDestinationRequest;
use OpenRouter\ValueObjects\Transporter\Payload;

final class Observability implements ObservabilityContract
{
    use Concerns\Paginates;
    use Concerns\Transportable;

    private const RESOURCE = 'observability/destinations';

    public function list(
        ?int $limit = null,
        ?int $offset = null,
        ?string $workspaceId = null,
    ): ListObservabilityDestinationsResponse {
        $query = self::page($limit, $offset);

        if ($workspaceId !== null) {
            $query['workspace_id'] = $workspaceId;
        }

        $response = $this->transporter->requestObject(Payload::list(self::RESOURCE, $query));

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return ListObservabilityDestinationsResponse::from($data, $response->meta());
    }

    /**
     * @param  CreateObservabilityDestinationRequest|array<string, mixed>  $parameters
     */
    public function create(CreateObservabilityDestinationRequest|array $parameters): ObservabilityDestinationResponse
    {
        $params = $parameters instanceof CreateObservabilityDestinationRequest
            ? $parameters->toArray()
            : $parameters;

        return $this->single(Payload::create(self::RESOURCE, $params));
    }

    public function retrieve(string $id): ObservabilityDestinationResponse
    {
        return $this->single(Payload::retrieve(self::RESOURCE, $id));
    }

    /**
     * @param  UpdateObservabilityDestinationRequest|array<string, mixed>  $parameters
     */
    public function update(string $id, UpdateObservabilityDestinationRequest|array $parameters): ObservabilityDestinationResponse
    {
        $params = $parameters instanceof UpdateObservabilityDestinationRequest
            ? $parameters->toArray()
            : $parameters;

        return $this->single(Payload::modify(self::RESOURCE, $id, $params));
    }

    public function delete(string $id): DeleteObservabilityDestinationResponse
    {
        $response = $this->transporter->requestObject(Payload::delete(self::RESOURCE, $id));

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return DeleteObservabilityDestinationResponse::from($data, $response->meta());
    }

    private function single(Payload $payload): ObservabilityDestinationResponse
    {
        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return ObservabilityDestinationResponse::from($data, $response->meta());
    }
}
