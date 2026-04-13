<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses;

/**
 * Dispatches a raw output item payload to the correct
 * {@see CreateResponseOutputItem} implementation based on its `type`
 * discriminator. Unknown types fall back to {@see CreateResponseOutputUnknown}
 * to preserve forward compatibility.
 */
final class CreateResponseOutputItemFactory
{
    /**
     * @param  array<string, mixed>  $item
     */
    public static function from(array $item): CreateResponseOutputItem
    {
        $type = is_string($item['type'] ?? null) ? $item['type'] : '';

        /** @phpstan-ignore-next-line — each subclass's ::from() validates the shape at runtime */
        return match ($type) {
            'message' => CreateResponseOutputMessage::from($item),
            'reasoning' => CreateResponseOutputReasoning::from($item),
            'function_call' => CreateResponseOutputFunctionCall::from($item),
            'web_search_call' => CreateResponseOutputWebSearchCall::from($item),
            'file_search_call' => CreateResponseOutputFileSearchCall::from($item),
            'image_generation_call' => CreateResponseOutputImageGenerationCall::from($item),
            'openrouter:datetime' => CreateResponseOutputDatetime::from($item),
            'openrouter:web_search' => CreateResponseOutputWebSearch::from($item),
            default => CreateResponseOutputUnknown::from($item),
        };
    }
}
