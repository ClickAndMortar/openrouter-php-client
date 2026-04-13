<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Content;

/**
 * `type: compaction` — emitted when the server compacts prior context to
 * make room. `content` is the compacted summary (may be null while the
 * compaction is still in progress).
 */
final class CompactionBlock implements MessagesContentBlock
{
    public function __construct(
        public readonly ?string $content = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            content: isset($attributes['content']) && is_string($attributes['content'])
                ? $attributes['content']
                : null,
        );
    }

    public function type(): string
    {
        return 'compaction';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type(),
            'content' => $this->content,
        ];
    }
}
