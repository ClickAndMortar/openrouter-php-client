<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Content;

use OpenRouter\ValueObjects\Messages\Citations\CitationFactory;
use OpenRouter\ValueObjects\Messages\Citations\MessagesCitation;

/**
 * `type: text` — a plain-text content block. Used on both the request side
 * (input text in a user/assistant message) and the response side (assistant's
 * text output).
 */
final class TextBlock implements MessagesContentBlock
{
    /**
     * @param  list<MessagesCitation|array<string, mixed>>|null  $citations
     */
    public function __construct(
        public readonly string $text,
        public readonly ?MessagesCacheControl $cacheControl = null,
        public readonly ?array $citations = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $citations = null;
        if (isset($attributes['citations']) && is_array($attributes['citations'])) {
            $citations = CitationFactory::fromList($attributes['citations']);
        }

        return new self(
            text: is_string($attributes['text'] ?? null) ? $attributes['text'] : '',
            cacheControl: isset($attributes['cache_control']) && is_array($attributes['cache_control'])
                ? MessagesCacheControl::from($attributes['cache_control'])
                : null,
            citations: $citations,
        );
    }

    public function type(): string
    {
        return 'text';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'type' => $this->type(),
            'text' => $this->text,
        ];

        if ($this->cacheControl !== null) {
            $data['cache_control'] = $this->cacheControl->toArray();
        }

        if ($this->citations !== null) {
            $data['citations'] = array_map(
                static fn (MessagesCitation|array $c): array => $c instanceof MessagesCitation
                    ? $c->toArray()
                    : $c,
                $this->citations,
            );
        }

        return $data;
    }
}
