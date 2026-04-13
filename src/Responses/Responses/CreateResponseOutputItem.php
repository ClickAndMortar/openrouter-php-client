<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses;

/**
 * Contract for an item in the `output` array of a {@see CreateResponse}.
 * Concrete implementations correspond to the discriminated union defined by
 * the `OutputItems` schema in the OpenRouter OpenAPI spec: message, reasoning,
 * function_call, web_search_call, file_search_call, image_generation_call,
 * openrouter:datetime, openrouter:web_search — with {@see CreateResponseOutputUnknown}
 * as a forward-compat fallback for new types.
 */
interface CreateResponseOutputItem
{
    /**
     * The discriminator value from the spec (e.g. `message`, `function_call`).
     */
    public function type(): string;

    public function id(): string;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
