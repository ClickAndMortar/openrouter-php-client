<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Input\Content;

/**
 * Text content part inside an input message. Mirrors the `InputText` schema
 * from the OpenRouter OpenAPI spec.
 */
final class InputTextPart implements FunctionCallOutputContentPart
{
    public function __construct(public readonly string $text)
    {
    }

    public function type(): string
    {
        return 'input_text';
    }

    /**
     * @return array{type: string, text: string}
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type(),
            'text' => $this->text,
        ];
    }
}
