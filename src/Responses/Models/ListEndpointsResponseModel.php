<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Models;

/**
 * @phpstan-import-type ListResponseModelArchitectureType from ListResponseModelArchitecture
 * @phpstan-import-type ListEndpointsResponseEndpointType from ListEndpointsResponseEndpoint
 *
 * @phpstan-type ListEndpointsResponseModelType array{
 *     id: string,
 *     name: string,
 *     created: int,
 *     description: string,
 *     architecture: ListResponseModelArchitectureType,
 *     endpoints: array<int, ListEndpointsResponseEndpointType>,
 * }
 */
final class ListEndpointsResponseModel
{
    /**
     * @param  array<int, ListEndpointsResponseEndpoint>  $endpoints
     */
    private function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly int $created,
        public readonly string $description,
        public readonly ListResponseModelArchitecture $architecture,
        public readonly array $endpoints,
    ) {
    }

    /**
     * @param  ListEndpointsResponseModelType  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            id: $attributes['id'],
            name: $attributes['name'],
            created: $attributes['created'],
            description: $attributes['description'],
            architecture: ListResponseModelArchitecture::from($attributes['architecture']),
            endpoints: array_map(
                static fn (array $endpoint): ListEndpointsResponseEndpoint => ListEndpointsResponseEndpoint::from($endpoint),
                $attributes['endpoints'],
            ),
        );
    }

    /**
     * @return ListEndpointsResponseModelType
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'created' => $this->created,
            'description' => $this->description,
            'architecture' => $this->architecture->toArray(),
            'endpoints' => array_map(
                static fn (ListEndpointsResponseEndpoint $endpoint): array => $endpoint->toArray(),
                $this->endpoints,
            ),
        ];
    }
}
