<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Chat\Config;

/**
 * Debug options for inspecting request transformations (streaming only).
 * Mirrors `ChatDebugOptions`.
 */
final class ChatDebugOptions
{
    public function __construct(
        public readonly ?bool $echoUpstreamBody = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            echoUpstreamBody: isset($attributes['echo_upstream_body']) ? (bool) $attributes['echo_upstream_body'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];
        if ($this->echoUpstreamBody !== null) {
            $data['echo_upstream_body'] = $this->echoUpstreamBody;
        }

        return $data;
    }
}
