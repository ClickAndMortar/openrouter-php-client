<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat\Content;

use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * Anthropic-style cache control directive. Mirrors the
 * `AnthropicCacheControlDirective` schema, used by `ChatRequest::cache_control`
 * and by `ChatTextPart::cache_control` (`ChatContentCacheControl`).
 */
final class ChatCacheControl
{
    public const TYPES = ['ephemeral'];

    public const TTLS = ['5m', '1h'];

    public function __construct(
        public readonly string $type = 'ephemeral',
        public readonly ?string $ttl = null,
    ) {
        if (! in_array($this->type, self::TYPES, true)) {
            throw new InvalidArgumentException(sprintf(
                'ChatCacheControl::$type must be one of %s, got "%s"',
                implode('/', self::TYPES),
                $this->type,
            ));
        }

        if ($this->ttl !== null && ! in_array($this->ttl, self::TTLS, true)) {
            throw new InvalidArgumentException(sprintf(
                'ChatCacheControl::$ttl must be one of %s or null, got "%s"',
                implode('/', self::TTLS),
                $this->ttl,
            ));
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            type: is_string($attributes['type'] ?? null) ? $attributes['type'] : 'ephemeral',
            ttl: isset($attributes['ttl']) && is_string($attributes['ttl']) ? $attributes['ttl'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['type' => $this->type];
        if ($this->ttl !== null) {
            $data['ttl'] = $this->ttl;
        }

        return $data;
    }
}
