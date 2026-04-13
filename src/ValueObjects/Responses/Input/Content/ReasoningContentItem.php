<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Input\Content;

/**
 * One item in a reasoning `content` array. The spec defines a discriminated
 * union over `reasoning.text`, `reasoning.summary`, and `reasoning.encrypted`.
 *
 * Models all three with optional fields rather than three subclasses — the
 * surface is small enough that polymorphism would be over-engineering.
 * Unknown discriminator values are preserved unchanged.
 */
final class ReasoningContentItem
{
    public const TYPE_TEXT = 'reasoning.text';

    public const TYPE_SUMMARY = 'reasoning.summary';

    public const TYPE_ENCRYPTED = 'reasoning.encrypted';

    /**
     * @param  array<string, mixed>  $extra
     */
    public function __construct(
        public readonly string $type,
        public readonly ?string $text = null,
        public readonly ?string $summary = null,
        public readonly ?string $data = null,
        public readonly ?string $format = null,
        public readonly array $extra = [],
    ) {
    }

    public static function text(string $text): self
    {
        return new self(type: self::TYPE_TEXT, text: $text);
    }

    public static function summary(string $summary): self
    {
        return new self(type: self::TYPE_SUMMARY, summary: $summary);
    }

    public static function encrypted(string $data, ?string $format = null): self
    {
        return new self(type: self::TYPE_ENCRYPTED, data: $data, format: $format);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $known = ['type', 'text', 'summary', 'data', 'format'];
        $extra = array_diff_key($attributes, array_flip($known));

        return new self(
            type: is_string($attributes['type'] ?? null) ? $attributes['type'] : 'unknown',
            text: isset($attributes['text']) && is_string($attributes['text']) ? $attributes['text'] : null,
            summary: isset($attributes['summary']) && is_string($attributes['summary']) ? $attributes['summary'] : null,
            data: isset($attributes['data']) && is_string($attributes['data']) ? $attributes['data'] : null,
            format: isset($attributes['format']) && is_string($attributes['format']) ? $attributes['format'] : null,
            extra: $extra,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['type' => $this->type];

        if ($this->text !== null) {
            $data['text'] = $this->text;
        }
        if ($this->summary !== null) {
            $data['summary'] = $this->summary;
        }
        if ($this->data !== null) {
            $data['data'] = $this->data;
        }
        if ($this->format !== null) {
            $data['format'] = $this->format;
        }

        foreach ($this->extra as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }
}
