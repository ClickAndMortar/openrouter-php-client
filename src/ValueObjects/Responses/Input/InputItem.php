<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Input;

/**
 * Contract for an item in the `input` array of a `CreateResponseRequest`.
 * Concrete implementations correspond to the discriminated union defined by
 * the OpenRouter OpenAPI spec: message, function_call, function_call_output,
 * reasoning.
 */
interface InputItem
{
    /**
     * The discriminator value used in the OpenAPI schema (e.g. `message`).
     */
    public function type(): string;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
