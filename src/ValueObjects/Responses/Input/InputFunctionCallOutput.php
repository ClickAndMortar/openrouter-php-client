<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Input;

use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\ValueObjects\Responses\Input\Content\FunctionCallOutputContentPart;

/**
 * A function_call_output input item — the result of a tool invocation that
 * the caller ran locally and is handing back to the model. Mirrors the
 * `FunctionCallOutputItem` schema from the OpenRouter OpenAPI spec.
 *
 * `output` is typically a JSON string the caller built from the tool result,
 * but the spec also accepts a list of content parts (text, image, or file —
 * audio and video are rejected by the API).
 */
final class InputFunctionCallOutput implements InputItem
{
    /**
     * @param  string|list<FunctionCallOutputContentPart>  $output
     */
    public function __construct(
        public readonly string $callId,
        public readonly string|array $output,
        public readonly ?string $id = null,
        public readonly ?string $status = null,
    ) {
        if (is_array($this->output)) {
            foreach ($this->output as $index => $part) {
                if (! $part instanceof FunctionCallOutputContentPart) {
                    throw new InvalidArgumentException(sprintf(
                        'InputFunctionCallOutput::$output[%d] must be a FunctionCallOutputContentPart (text/image/file), got %s',
                        $index,
                        is_object($part) ? $part::class : gettype($part),
                    ));
                }
            }
        }
    }

    public function type(): string
    {
        return 'function_call_output';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'type' => $this->type(),
            'call_id' => $this->callId,
            'output' => is_string($this->output)
                ? $this->output
                : array_map(
                    static fn (FunctionCallOutputContentPart $part): array => $part->toArray(),
                    $this->output,
                ),
        ];

        if ($this->id !== null) {
            $data['id'] = $this->id;
        }
        if ($this->status !== null) {
            $data['status'] = $this->status;
        }

        return $data;
    }
}
