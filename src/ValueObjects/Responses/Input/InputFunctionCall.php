<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Input;

/**
 * A function_call input item — used on follow-up requests to echo the tool
 * invocation the model produced on a previous turn. Mirrors the
 * `FunctionCallItem` schema from the OpenRouter OpenAPI spec.
 *
 * `arguments` is a raw JSON string as emitted by the model, not a decoded
 * associative array.
 */
final class InputFunctionCall implements InputItem
{
    public function __construct(
        public readonly string $callId,
        public readonly string $name,
        public readonly string $arguments,
        public readonly ?string $id = null,
        public readonly ?string $status = null,
    ) {
    }

    public function type(): string
    {
        return 'function_call';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'type' => $this->type(),
            'call_id' => $this->callId,
            'name' => $this->name,
            'arguments' => $this->arguments,
        ];

        if ($this->id !== null) {
            $data['id'] = $this->id;
        }
        if ($this->status !== null) {
            $data['status'] = $this->status;
        }

        return $data;
    }
}
