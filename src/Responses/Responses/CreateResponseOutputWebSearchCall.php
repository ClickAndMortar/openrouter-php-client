<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses;

/**
 * A `web_search_call`-typed item inside the response `output` array. Mirrors
 * the `OutputItemWebSearchCall` schema from the OpenRouter OpenAPI spec.
 *
 * `action` is a discriminated union (search / open_page / find_in_page) kept
 * as a raw array in V1.1 — typing it is a P1 follow-up.
 *
 * @phpstan-type CreateResponseOutputWebSearchCallType array{
 *     id: string,
 *     type: string,
 *     status: string,
 *     action?: array<string, mixed>|null,
 * }
 */
final class CreateResponseOutputWebSearchCall implements CreateResponseOutputItem
{
    /**
     * @param  array<string, mixed>|null  $action
     */
    private function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly ?array $action,
    ) {
    }

    /**
     * @param  CreateResponseOutputWebSearchCallType  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            id: $attributes['id'],
            status: $attributes['status'],
            action: $attributes['action'] ?? null,
        );
    }

    public function type(): string
    {
        return 'web_search_call';
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
        $data = [
            'id' => $this->id,
            'type' => $this->type(),
            'status' => $this->status,
        ];

        if ($this->action !== null) {
            $data['action'] = $this->action;
        }

        return $data;
    }
}
