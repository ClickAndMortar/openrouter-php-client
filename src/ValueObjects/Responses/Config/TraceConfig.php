<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Config;

/**
 * Typed builder for the `trace` request field. The OpenAPI schema is
 * `additionalProperties: true` — known keys get typed accessors, anything
 * else is preserved through `$customMetadata`.
 */
final class TraceConfig
{
    /**
     * @param  array<string, mixed>  $customMetadata
     */
    public function __construct(
        public readonly ?string $traceId = null,
        public readonly ?string $traceName = null,
        public readonly ?string $spanName = null,
        public readonly ?string $generationName = null,
        public readonly ?string $parentSpanId = null,
        public readonly array $customMetadata = [],
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $known = ['trace_id', 'trace_name', 'span_name', 'generation_name', 'parent_span_id'];
        $custom = array_diff_key($attributes, array_flip($known));

        return new self(
            traceId: isset($attributes['trace_id']) && is_string($attributes['trace_id']) ? $attributes['trace_id'] : null,
            traceName: isset($attributes['trace_name']) && is_string($attributes['trace_name']) ? $attributes['trace_name'] : null,
            spanName: isset($attributes['span_name']) && is_string($attributes['span_name']) ? $attributes['span_name'] : null,
            generationName: isset($attributes['generation_name']) && is_string($attributes['generation_name']) ? $attributes['generation_name'] : null,
            parentSpanId: isset($attributes['parent_span_id']) && is_string($attributes['parent_span_id']) ? $attributes['parent_span_id'] : null,
            customMetadata: $custom,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];

        if ($this->traceId !== null) {
            $data['trace_id'] = $this->traceId;
        }
        if ($this->traceName !== null) {
            $data['trace_name'] = $this->traceName;
        }
        if ($this->spanName !== null) {
            $data['span_name'] = $this->spanName;
        }
        if ($this->generationName !== null) {
            $data['generation_name'] = $this->generationName;
        }
        if ($this->parentSpanId !== null) {
            $data['parent_span_id'] = $this->parentSpanId;
        }

        foreach ($this->customMetadata as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }
}
