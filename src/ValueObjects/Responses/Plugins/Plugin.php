<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Plugins;

/**
 * Contract for a single entry in the `plugins` array of a
 * `CreateResponseRequest`. Concrete implementations correspond to the
 * discriminated union defined by the OpenRouter OpenAPI spec (auto-router,
 * moderation, web, file-parser, response-healing, context-compression).
 * Unknown discriminator values fall back to {@see UnknownPlugin}.
 */
interface Plugin
{
    /**
     * The discriminator value used in the OpenAPI schema (e.g. `web`).
     */
    public function id(): string;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
