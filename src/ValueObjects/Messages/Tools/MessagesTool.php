<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Tools;

/**
 * Contract for a single entry in `MessagesRequest.tools[]`. Concrete
 * implementations correspond to the discriminated union (custom,
 * bash_20250124, text_editor_20250124, web_search_20250305,
 * web_search_20260209, openrouter:datetime, openrouter:web_search). Unknown
 * discriminator values fall back to {@see UnknownMessagesTool}.
 */
interface MessagesTool
{
    /**
     * The `type` discriminator used in the OpenAPI schema.
     */
    public function type(): string;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
