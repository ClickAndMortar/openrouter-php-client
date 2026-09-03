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
        /** @var array<string, mixed> */
        public readonly array $extras = [],
    ) {
    }

    /**
     * @param  ListResponseModelArchitectureType  $attributes
     */
    public static function from(array $attributes): self
    {
        $extras = array_diff_key($attributes, array_flip([
            'modality',
            'input_modalities',
            'output_modalities',
            'instruct_type',
            'tokenizer',
        ]));

        return new self(
            modality: $attributes['modality'] ?? null,
            inputModalities: $attributes['input_modalities'],
            outputModalities: $attributes['output_modalities'],
            instructType: $attributes['instruct_type'] ?? null,
            tokenizer: $attributes['tokenizer'] ?? null,
            extras: $extras,
        );
    }

    /**
     * @return ListResponseModelArchitectureType
     */
    public function toArray(): array
    {
        $data = [
            'modality' => $this->modality,
            'input_modalities' => $this->inputModalities,
            'output_modalities' => $this->outputModalities,
            'instruct_type' => $this->instructType,
            'tokenizer' => $this->tokenizer,
        ];

        return [...$data, ...$this->extras];
    }
}
