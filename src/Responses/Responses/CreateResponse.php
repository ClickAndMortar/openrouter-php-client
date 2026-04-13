<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses;

use OpenRouter\Contracts\ResponseContract;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Responses\Concerns\ArrayAccessible;
use OpenRouter\Responses\Concerns\HasMetaInformation;
use OpenRouter\Responses\Meta\MetaInformation;

/**
 * Typed wrapper around OpenRouter's `OpenResponsesResult` schema for non-streaming
 * `/responses` calls. The main scalar fields and the usage value object are strictly
 * typed; `$output` holds a polymorphic list of {@see CreateResponseOutputItem}
 * implementations (one per spec discriminator), with a forward-compat
 * {@see CreateResponseOutputUnknown} fallback for unknown types. The raw
 * server payload is preserved on `$rawOutput` for opt-out access.
 *
 * @phpstan-type CreateResponseType array{
 *     id: string,
 *     object: string,
 *     created_at: int,
 *     model: string,
 *     status: string,
 *     output: array<int, array<string, mixed>>,
 *     usage?: array<string, mixed>,
 *     error?: array{code: string, message: string}|null,
 *     incomplete_details?: array{reason?: string}|null,
 *     instructions?: mixed,
 *     metadata?: array<string, mixed>|null,
 *     max_output_tokens?: int|null,
 *     temperature?: float|null,
 *     top_p?: float|null,
 *     tool_choice?: mixed,
 *     tools?: array<int, mixed>,
 *     parallel_tool_calls?: bool,
 *     service_tier?: string|null,
 *     previous_response_id?: string|null,
 *     output_text?: string,
 *     background?: bool|null,
 *     store?: bool,
 * }
 *
 * @implements ResponseContract<CreateResponseType>
 */
final class CreateResponse implements ResponseContract, ResponseHasMetaInformationContract
{
    /** @use ArrayAccessible<CreateResponseType> */
    use ArrayAccessible;
    use HasMetaInformation;

    /**
     * @param  array<int, CreateResponseOutputItem>  $output
     * @param  array<int, array<string, mixed>>  $rawOutput
     * @param  array<string, mixed>|null  $metadata
     * @param  array<int, mixed>  $tools
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly string $id,
        public readonly string $object,
        public readonly int $createdAt,
        public readonly string $model,
        public readonly string $status,
        public readonly array $output,
        public readonly array $rawOutput,
        public readonly ?CreateResponseUsage $usage,
        public readonly ?CreateResponseError $error,
        public readonly ?CreateResponseIncompleteDetails $incompleteDetails,
        public readonly mixed $instructions,
        public readonly ?array $metadata,
        public readonly ?int $maxOutputTokens,
        public readonly ?float $temperature,
        public readonly ?float $topP,
        public readonly mixed $toolChoice,
        public readonly array $tools,
        public readonly ?bool $parallelToolCalls,
        public readonly ?string $serviceTier,
        public readonly ?string $previousResponseId,
        public readonly ?string $outputText,
        public readonly ?bool $background,
        public readonly ?bool $store,
        public readonly array $extras,
        private readonly MetaInformation $meta,
    ) {
    }

    /**
     * @param  CreateResponseType  $attributes
     */
    public static function from(array $attributes, MetaInformation $meta): self
    {
        $rawOutput = $attributes['output'] ?? [];

        $output = array_values(array_map(
            static fn (array $item): CreateResponseOutputItem => CreateResponseOutputItemFactory::from($item),
            $rawOutput,
        ));

        $known = [
            'id', 'object', 'created_at', 'model', 'status', 'output', 'usage', 'error',
            'incomplete_details', 'instructions', 'metadata', 'max_output_tokens',
            'temperature', 'top_p', 'tool_choice', 'tools', 'parallel_tool_calls',
            'service_tier', 'previous_response_id', 'output_text', 'background', 'store',
        ];
        $extras = array_diff_key($attributes, array_flip($known));

        return new self(
            id: $attributes['id'],
            object: $attributes['object'],
            createdAt: $attributes['created_at'],
            model: $attributes['model'],
            status: $attributes['status'],
            output: $output,
            rawOutput: $rawOutput,
            usage: isset($attributes['usage']) ? CreateResponseUsage::from($attributes['usage']) : null,
            error: isset($attributes['error']) && $attributes['error'] !== null
                ? CreateResponseError::from($attributes['error'])
                : null,
            incompleteDetails: isset($attributes['incomplete_details']) && $attributes['incomplete_details'] !== null
                ? CreateResponseIncompleteDetails::from($attributes['incomplete_details'])
                : null,
            instructions: $attributes['instructions'] ?? null,
            metadata: $attributes['metadata'] ?? null,
            maxOutputTokens: $attributes['max_output_tokens'] ?? null,
            temperature: $attributes['temperature'] ?? null,
            topP: $attributes['top_p'] ?? null,
            toolChoice: $attributes['tool_choice'] ?? null,
            tools: $attributes['tools'] ?? [],
            parallelToolCalls: $attributes['parallel_tool_calls'] ?? null,
            serviceTier: $attributes['service_tier'] ?? null,
            previousResponseId: $attributes['previous_response_id'] ?? null,
            outputText: $attributes['output_text'] ?? null,
            background: $attributes['background'] ?? null,
            store: $attributes['store'] ?? null,
            extras: $extras,
            meta: $meta,
        );
    }

    /**
     * @return CreateResponseType
     */
    public function toArray(): array
    {
        $data = [
            'id' => $this->id,
            'object' => $this->object,
            'created_at' => $this->createdAt,
            'model' => $this->model,
            'status' => $this->status,
            'output' => $this->rawOutput,
        ];

        if ($this->usage !== null) {
            $data['usage'] = $this->usage->toArray();
        }

        if ($this->error !== null) {
            $data['error'] = $this->error->toArray();
        }

        if ($this->incompleteDetails !== null) {
            $data['incomplete_details'] = $this->incompleteDetails->toArray();
        }

        foreach ([
            'instructions' => $this->instructions,
            'metadata' => $this->metadata,
            'max_output_tokens' => $this->maxOutputTokens,
            'temperature' => $this->temperature,
            'top_p' => $this->topP,
            'tool_choice' => $this->toolChoice,
            'parallel_tool_calls' => $this->parallelToolCalls,
            'service_tier' => $this->serviceTier,
            'previous_response_id' => $this->previousResponseId,
            'output_text' => $this->outputText,
            'background' => $this->background,
            'store' => $this->store,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        if ($this->tools !== []) {
            $data['tools'] = $this->tools;
        }

        /** @var CreateResponseType $data */
        $data = [...$data, ...$this->extras];

        return $data;
    }
}
