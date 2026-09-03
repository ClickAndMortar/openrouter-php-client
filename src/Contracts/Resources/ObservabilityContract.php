<?php

declare(strict_types=1);

namespace OpenRouter\Contracts\Resources;

use OpenRouter\Responses\Observability\DeleteObservabilityDestinationResponse;
use OpenRouter\Responses\Observability\ListObservabilityDestinationsResponse;
use OpenRouter\Responses\Observability\ObservabilityDestinationResponse;
use OpenRouter\ValueObjects\Observability\CreateObservabilityDestinationRequest;
use OpenRouter\ValueObjects\Observability\UpdateObservabilityDestinationRequest;

interface ObservabilityContract
{
    public function list(
        ?int $limit = null,
        ?int $offset = null,
        ?string $workspaceId = null,
    ): ListObservabilityDestinationsResponse;

    /**
     * @param  CreateObservabilityDestinationRequest|array<string, mixed>  $parameters
     */
    public function create(CreateObservabilityDestinationRequest|array $parameters): ObservabilityDestinationResponse;

    public function retrieve(string $id): ObservabilityDestinationResponse;

    /**
     * @param  UpdateObservabilityDestinationRequest|array<string, mixed>  $parameters
     */
    public function update(string $id, UpdateObservabilityDestinationRequest|array $parameters): ObservabilityDestinationResponse;

    public function delete(string $id): DeleteObservabilityDestinationResponse;
}
