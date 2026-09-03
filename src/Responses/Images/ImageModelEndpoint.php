<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Images;

/**
 * One provider endpoint serving an image model.
 */
final class ImageModelEndpoint
{
    /**
     * @param  list<string>|null  $supportedParameters
     * @param  list<string>|null  $allowedPassthroughParameters
     * @param  array<int, mixed>|null  $pricing
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly string $providerName,
        public readonly string $providerSlug,
        public readonly ?string $providerTag,
        public readonly ?array $supportedParameters,
        public readonly ?array $allowedPassthroughParameters,
        public readonly bool $supportsStreaming,
        public readonly ?array $pricing,
        public readonly array $extras = [],
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $strings = static fn (mixed $v): ?array => is_array($v) ? array_values(array_map('strval', $v)) : null;

        return new self(
            providerName: is_string($attributes['provider_name'] ?? null) ? $attributes['provider_name'] : '',
            providerSlug: is_string($attributes['provider_slug'] ?? null) ? $attributes['provider_slug'] : '',
            providerTag: is_string($attributes['provider_tag'] ?? null) ? $attributes['provider_tag'] : null,
            supportedParameters: $strings($attributes['supported_parameters'] ?? null),
            allowedPassthroughParameters: $strings($attributes['allowed_passthrough_parameters'] ?? null),
            supportsStreaming: (bool) ($attributes['supports_streaming'] ?? false),
            pricing: is_array($attributes['pricing'] ?? null) ? array_values($attributes['pricing']) : null,
            extras: array_diff_key($attributes, array_flip([
                'provider_name', 'provider_slug', 'provider_tag', 'supported_parameters',
                'allowed_passthrough_parameters', 'supports_streaming', 'pricing',
            ])),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'provider_name' => $this->providerName,
            'provider_slug' => $this->providerSlug,
            'provider_tag' => $this->providerTag,
            'supports_streaming' => $this->supportsStreaming,
        ];

        foreach ([
            'supported_parameters' => $this->supportedParameters,
            'allowed_passthrough_parameters' => $this->allowedPassthroughParameters,
            'pricing' => $this->pricing,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return [...$data, ...$this->extras];
    }
}
