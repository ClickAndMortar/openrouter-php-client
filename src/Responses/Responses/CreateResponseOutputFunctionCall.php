<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses;

/**
 * A `function_call`-typed item inside the response `output` array. Mirrors the
 * `OutputItemFunctionCall` schema from the OpenRouter OpenAPI spec.
 *
 * `arguments` is the raw JSON string emitted by the model, not a decoded
 * associative array — callers are expected to `json_decode()` it themselves.
 *
 * @phpstan-type CreateResponseOutputFunctionCallType array{
 *     id: string,
 *     type: string,
 *     call_id: string,
 *     name: string,
 *     arguments: string,
 *     status?: string|null,
 * }
 */
final class CreateResponseOutputFunctionCall implements CreateResponseOutputItem
{
    /** @var array<string, mixed>|null Memoised JSON-decoded arguments. */
    private ?array $decodedArguments = null;

    private function __construct(
        public readonly string $id,
        public readonly string $callId,
        public readonly string $name,
        public readonly string $arguments,
        public readonly ?string $status,
    ) {
    }

    /**
     * JSON-decoded `arguments`. Returns `[]` when empty or malformed — use
     * `$arguments` directly for strict decoding. Result is memoised.
     *
     * @return array<string, mixed>
     */
    public function decodedArguments(): array
    {
        if ($this->decodedArguments !== null) {
            return $this->decodedArguments;
        }

        if ($this->arguments === '') {
            return $this->decodedArguments = [];
        }

        $decoded = json_decode($this->arguments, true);

        return $this->decodedArguments = is_array($decoded) ? $decoded : [];
    }

    /**
     * @param  CreateResponseOutputFunctionCallType  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            id: $attributes['id'],
            callId: $attributes['call_id'],
            name: $attributes['name'],
            arguments: $attributes['arguments'],
            status: $attributes['status'] ?? null,
        );
    }

    public function type(): string
    {
        return 'function_call';
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
            'call_id' => $this->callId,
            'name' => $this->name,
            'arguments' => $this->arguments,
        ];

        if ($this->status !== null) {
            $data['status'] = $this->status;
        }

        return $data;
    }
}
