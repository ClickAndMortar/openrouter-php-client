<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses;

/**
 * An `image_generation_call`-typed item inside the response `output` array.
 * Mirrors the `OutputItemImageGenerationCall` schema from the OpenRouter
 * OpenAPI spec.
 *
 * `result` holds the base64-encoded image when generation completes, or null
 * while in progress.
 *
 * @phpstan-type CreateResponseOutputImageGenerationCallType array{
 *     id: string,
 *     type: string,
 *     status: string,
 *     result?: string|null,
 * }
 */
final class CreateResponseOutputImageGenerationCall implements CreateResponseOutputItem
{
    private function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly ?string $result,
    ) {
    }

    /**
     * @param  CreateResponseOutputImageGenerationCallType  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            id: $attributes['id'],
            status: $attributes['status'],
            result: $attributes['result'] ?? null,
        );
    }

    public function type(): string
    {
        return 'image_generation_call';
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

        if ($this->result !== null) {
            $data['result'] = $this->result;
        }

        return $data;
    }
}
