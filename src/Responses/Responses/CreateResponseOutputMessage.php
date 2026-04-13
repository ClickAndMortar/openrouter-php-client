<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses;

/**
 * A single `message`-typed item inside the response `output` array. Other output item
 * types (reasoning, function_call, web_search_call, etc.) are preserved in the raw
 * payload accessible via `CreateResponse::toArray()` but are not modeled as typed
 * value objects in v1.
 *
 * @phpstan-type CreateResponseOutputMessageType array{
 *     id: string,
 *     type: string,
 *     role: string,
 *     status?: string,
 *     phase?: string|null,
 *     content: array<int, array<string, mixed>>,
 * }
 */
final class CreateResponseOutputMessage implements CreateResponseOutputItem
{
    /**
     * @param  array<int, CreateResponseOutputContent>  $content
     * @param  string|null  $phase  When `commentary`, callers must preserve this
     *     message and resend it on follow-up requests for gpt-5.3-codex correctness.
     */
    private function __construct(
        public readonly string $id,
        public readonly string $type,
        public readonly string $role,
        public readonly ?string $status,
        public readonly ?string $phase,
        public readonly array $content,
    ) {
    }

    public function type(): string
    {
        return $this->type;
    }

    public function id(): string
    {
        return $this->id;
    }

    /**
     * @param  CreateResponseOutputMessageType  $attributes
     */
    public static function from(array $attributes): self
    {
        $content = array_map(
            static fn (array $item): CreateResponseOutputContent => CreateResponseOutputContent::from($item),
            $attributes['content'],
        );

        return new self(
            id: $attributes['id'],
            type: $attributes['type'],
            role: $attributes['role'],
            status: $attributes['status'] ?? null,
            phase: $attributes['phase'] ?? null,
            content: $content,
        );
    }

    /**
     * @return CreateResponseOutputMessageType
     */
    public function toArray(): array
    {
        $data = [
            'id' => $this->id,
            'type' => $this->type,
            'role' => $this->role,
            'content' => array_map(
                static fn (CreateResponseOutputContent $content): array => $content->toArray(),
                $this->content,
            ),
        ];

        if ($this->status !== null) {
            $data['status'] = $this->status;
        }

        if ($this->phase !== null) {
            $data['phase'] = $this->phase;
        }

        /** @var CreateResponseOutputMessageType $data */
        return $data;
    }
}
