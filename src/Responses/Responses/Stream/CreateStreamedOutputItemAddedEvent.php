<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses\Stream;

use OpenRouter\Responses\Responses\CreateResponseOutputItem;
use OpenRouter\Responses\Responses\CreateResponseOutputItemFactory;
use OpenRouter\Responses\Responses\CreateStreamedResponse;

/**
 * `response.output_item.added` — emitted when a new item (message, reasoning,
 * function_call, etc.) starts being produced. `$item` carries the typed
 * output item, `$outputIndex` its position in the `output` array.
 */
final class CreateStreamedOutputItemAddedEvent extends CreateStreamedResponse
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    private function __construct(
        array $attributes,
        public readonly ?CreateResponseOutputItem $item,
        public readonly int $outputIndex,
        public readonly int $sequenceNumber,
    ) {
        parent::__construct('response.output_item.added', $attributes);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload): self
    {
        $item = isset($payload['item']) && is_array($payload['item'])
            ? CreateResponseOutputItemFactory::from($payload['item'])
            : null;

        return new self(
            attributes: $payload,
            item: $item,
            outputIndex: (int) ($payload['output_index'] ?? 0),
            sequenceNumber: (int) ($payload['sequence_number'] ?? 0),
        );
    }
}
