<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses;

/**
 * An `openrouter:datetime`-typed item inside the response `output` array.
 * Mirrors the `OutputDatetimeItem` schema from the OpenRouter OpenAPI spec.
 *
 * Emitted when the `datetime` server tool is used — the model asked for the
 * current wall-clock time, and OpenRouter injected it.
 *
 * @phpstan-type CreateResponseOutputDatetimeType array{
 *     id: string,
 *     type: string,
 *     status: string,
 *     datetime: string,
 *     timezone: string,
 * }
 */
final class CreateResponseOutputDatetime implements CreateResponseOutputItem
{
    private function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly string $datetime,
        public readonly string $timezone,
    ) {
    }

    /**
     * @param  CreateResponseOutputDatetimeType  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            id: $attributes['id'],
            status: $attributes['status'],
            datetime: $attributes['datetime'],
            timezone: $attributes['timezone'],
        );
    }

    public function type(): string
    {
        return 'openrouter:datetime';
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
            'status' => $this->status,
            'datetime' => $this->datetime,
            'timezone' => $this->timezone,
        ];
    }
}
