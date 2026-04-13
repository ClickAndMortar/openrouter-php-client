<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Messages\Deltas;

final class CompactionDelta implements MessagesDelta
{
    public function __construct(
        public readonly ?string $content,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            content: isset($attributes['content']) && is_string($attributes['content']) ? $attributes['content'] : null,
        );
    }

    public function type(): string
    {
        return 'compaction_delta';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return ['type' => $this->type(), 'content' => $this->content];
    }
}
