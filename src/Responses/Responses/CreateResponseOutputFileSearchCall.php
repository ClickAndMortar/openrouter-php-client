<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses;

/**
 * A `file_search_call`-typed item inside the response `output` array. Mirrors
 * the `OutputItemFileSearchCall` schema from the OpenRouter OpenAPI spec.
 *
 * @phpstan-type CreateResponseOutputFileSearchCallType array{
 *     id: string,
 *     type: string,
 *     queries: array<int, string>,
 *     status: string,
 * }
 */
final class CreateResponseOutputFileSearchCall implements CreateResponseOutputItem
{
    /**
     * @param  list<string>  $queries
     */
    private function __construct(
        public readonly string $id,
        public readonly array $queries,
        public readonly string $status,
    ) {
    }

    /**
     * @param  CreateResponseOutputFileSearchCallType  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            id: $attributes['id'],
            queries: $attributes['queries'],
            status: $attributes['status'],
        );
    }

    public function type(): string
    {
        return 'file_search_call';
    }

    public function id(): string
    {
        return $this->id;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type(),
            'queries' => $this->queries,
            'status' => $this->status,
        ];
    }
}
