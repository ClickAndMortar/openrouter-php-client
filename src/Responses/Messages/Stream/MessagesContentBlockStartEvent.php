<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Messages\Stream;

use OpenRouter\Responses\Messages\MessagesStreamEvent;
use OpenRouter\ValueObjects\Messages\Content\ContentBlockFactory;
use OpenRouter\ValueObjects\Messages\Content\MessagesContentBlock;

/**
 * `content_block_start` — emitted when a new content block (text, tool_use,
 * thinking, etc.) is opened at the given `index` in the assistant message.
 */
final class MessagesContentBlockStartEvent extends MessagesStreamEvent
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    private function __construct(
        array $attributes,
        public readonly int $index,
        public readonly MessagesContentBlock $contentBlock,
    ) {
        parent::__construct('content_block_start', $attributes);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload): self
    {
        $rawBlock = is_array($payload['content_block'] ?? null) ? $payload['content_block'] : [];

        return new self(
            attributes: $payload,
            index: is_int($payload['index'] ?? null) ? $payload['index'] : 0,
            contentBlock: ContentBlockFactory::from($rawBlock),
        );
    }
}
