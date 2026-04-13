<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Models;

/**
 * @phpstan-type ListResponseModelArchitectureType array{
 *     modality?: string|null,
 *     input_modalities: array<int, string>,
 *     output_modalities: array<int, string>,
 *     instruct_type?: string|null,
 *     tokenizer?: string|null,
 * }
 */
final class ListResponseModelArchitecture
{
    /**
     * @param  array<int, string>  $inputModalities
     * @param  array<int, string>  $outputModalities
     */
    private function __construct(
        public readonly ?string $modality,
        public readonly array $inputModalities,
        public readonly array $outputModalities,
        public readonly ?string $instructType,
        public readonly ?string $tokenizer,
    ) {
    }

    /**
     * @param  ListResponseModelArchitectureType  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            modality: $attributes['modality'] ?? null,
            inputModalities: $attributes['input_modalities'],
            outputModalities: $attributes['output_modalities'],
            instructType: $attributes['instruct_type'] ?? null,
            tokenizer: $attributes['tokenizer'] ?? null,
        );
    }

    /**
     * @return ListResponseModelArchitectureType
     */
    public function toArray(): array
    {
        return [
            'modality' => $this->modality,
            'input_modalities' => $this->inputModalities,
            'output_modalities' => $this->outputModalities,
            'instruct_type' => $this->instructType,
            'tokenizer' => $this->tokenizer,
        ];
    }
}
