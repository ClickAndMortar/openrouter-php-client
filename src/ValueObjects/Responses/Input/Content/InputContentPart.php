<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Input\Content;

/**
 * Contract for a single content part inside an input message. The concrete
 * implementations correspond to the discriminated union documented in the
 * OpenRouter OpenAPI spec: InputText, InputImage, InputFile, InputAudio,
 * InputVideo.
 */
interface InputContentPart
{
    /**
     * The discriminator value used in the OpenAPI schema (e.g. `input_text`).
     */
    public function type(): string;

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
