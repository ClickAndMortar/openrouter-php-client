<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Tools;

/**
 * Contract for a single entry in the `tools` array of a `CreateResponseRequest`.
 * Concrete implementations correspond to the discriminated union defined by
 * the OpenRouter OpenAPI spec (function, web_search_2025_08_26, file_search,
 * mcp, image_generation, code_interpreter, computer_use_preview, shell,
 * local_shell, apply_patch, openrouter:datetime, openrouter:web_search,
 * custom). Unknown discriminator values fall back to {@see UnknownTool}.
 */
interface Tool
{
    /**
     * The discriminator value used in the OpenAPI schema (e.g. `function`).
     */
    public function type(): string;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
