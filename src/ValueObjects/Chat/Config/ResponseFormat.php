<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat\Config;

/**
 * Contract for the chat `response_format` request field. Concrete
 * implementations correspond to the discriminated union (text, json_object,
 * json_schema, grammar, python). Unknown discriminators fall back to
 * {@see UnknownResponseFormat}.
 */
interface ResponseFormat
{
    public function type(): string;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
