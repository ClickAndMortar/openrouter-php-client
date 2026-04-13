<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses;

/**
 * Typed annotation attached to an `output_text` content item. Today the spec
 * only defines `url_citation`; unknown types are preserved as-is in `$extra`.
 *
 * @phpstan-type CreateResponseOutputAnnotationType array{
 *     type: string,
 *     start_index?: int,
 *     end_index?: int,
 *     url?: string,
 *     title?: string,
 * }
 */
final class CreateResponseOutputAnnotation
{
    /**
     * @param  array<string, mixed>  $extra
     */
    private function __construct(
        public readonly string $type,
        public readonly ?int $startIndex,
        public readonly ?int $endIndex,
        public readonly ?string $url,
        public readonly ?string $title,
        public readonly array $extra,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $known = ['type', 'start_index', 'end_index', 'url', 'title'];
        $extra = array_diff_key($attributes, array_flip($known));

        return new self(
            type: is_string($attributes['type'] ?? null) ? $attributes['type'] : 'unknown',
            startIndex: isset($attributes['start_index']) ? (int) $attributes['start_index'] : null,
            endIndex: isset($attributes['end_index']) ? (int) $attributes['end_index'] : null,
            url: isset($attributes['url']) && is_string($attributes['url']) ? $attributes['url'] : null,
            title: isset($attributes['title']) && is_string($attributes['title']) ? $attributes['title'] : null,
            extra: $extra,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['type' => $this->type];

        if ($this->startIndex !== null) {
            $data['start_index'] = $this->startIndex;
        }
        if ($this->endIndex !== null) {
            $data['end_index'] = $this->endIndex;
        }
        if ($this->url !== null) {
            $data['url'] = $this->url;
        }
        if ($this->title !== null) {
            $data['title'] = $this->title;
        }

        foreach ($this->extra as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }
}
