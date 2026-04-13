<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Content;

/**
 * `type: document` — request-side document attachment (PDFs, text). `source`
 * is a discriminated union (base64 / url / text / content) passed through as
 * a raw array. `context` and `title` are free-text metadata; `citations` is
 * the opt-in shape `{enabled: bool}`.
 */
final class DocumentBlock implements MessagesContentBlock
{
    /**
     * @param  array<string, mixed>  $source
     * @param  array<string, mixed>|null  $citations
     */
    public function __construct(
        public readonly array $source,
        public readonly ?string $title = null,
        public readonly ?string $context = null,
        public readonly ?array $citations = null,
        public readonly ?MessagesCacheControl $cacheControl = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            source: is_array($attributes['source'] ?? null) ? $attributes['source'] : [],
            title: isset($attributes['title']) && is_string($attributes['title']) ? $attributes['title'] : null,
            context: isset($attributes['context']) && is_string($attributes['context']) ? $attributes['context'] : null,
            citations: isset($attributes['citations']) && is_array($attributes['citations'])
                ? $attributes['citations']
                : null,
            cacheControl: isset($attributes['cache_control']) && is_array($attributes['cache_control'])
                ? MessagesCacheControl::from($attributes['cache_control'])
                : null,
        );
    }

    public function type(): string
    {
        return 'document';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'type' => $this->type(),
            'source' => $this->source,
        ];

        foreach ([
            'title' => $this->title,
            'context' => $this->context,
            'citations' => $this->citations,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        if ($this->cacheControl !== null) {
            $data['cache_control'] = $this->cacheControl->toArray();
        }

        return $data;
    }
}
