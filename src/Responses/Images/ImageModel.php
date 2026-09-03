<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Images;

/**
 * An image generation model. `supportsStreaming` says whether
 * `$client->images()->generateStreamed()` works for it.
 */
final class ImageModel
{
    /**
     * @param  array<string, mixed>|null  $architecture
     * @param  list<string>|null  $supportedParameters
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $description,
        public readonly int $created,
        public readonly ?array $architecture,
        public readonly ?array $supportedParameters,
        public readonly bool $supportsStreaming,
        public readonly ?string $endpoints,
        public readonly array $extras = [],
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        /** @var list<string>|null $params */
        $params = is_array($attributes['supported_parameters'] ?? null)
            ? array_values(array_map('strval', $attributes['supported_parameters']))
            : null;

        return new self(
            id: is_string($attributes['id'] ?? null) ? $attributes['id'] : '',
            name: is_string($attributes['name'] ?? null) ? $attributes['name'] : '',
            description: is_string($attributes['description'] ?? null) ? $attributes['description'] : null,
            created: is_int($attributes['created'] ?? null) ? $attributes['created'] : 0,
            architecture: is_array($attributes['architecture'] ?? null) ? $attributes['architecture'] : null,
            supportedParameters: $params,
            supportsStreaming: (bool) ($attributes['supports_streaming'] ?? false),
            endpoints: is_string($attributes['endpoints'] ?? null) ? $attributes['endpoints'] : null,
            extras: array_diff_key($attributes, array_flip([
                'id', 'name', 'description', 'created', 'architecture',
                'supported_parameters', 'supports_streaming', 'endpoints',
            ])),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'created' => $this->created,
            'supports_streaming' => $this->supportsStreaming,
        ];

        foreach ([
            'description' => $this->description,
            'architecture' => $this->architecture,
            'supported_parameters' => $this->supportedParameters,
            'endpoints' => $this->endpoints,
        ] as $key => $value) {
            if ($value !== null) {
                $data[$key] = $value;
            }
        }

        return [...$data, ...$this->extras];
    }
}
