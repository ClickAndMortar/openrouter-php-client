<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Videos;

final class VideoUsage
{
    /**
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly ?float $cost,
        public readonly ?bool $isByok,
        public readonly array $extras = [],
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            isset($attributes['cost']) && is_numeric($attributes['cost']) ? (float) $attributes['cost'] : null,
            isset($attributes['is_byok']) ? (bool) $attributes['is_byok'] : null,
            array_diff_key($attributes, array_flip(['cost', 'is_byok'])),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];

        if ($this->cost !== null) {
            $data['cost'] = $this->cost;
        }
        if ($this->isByok !== null) {
            $data['is_byok'] = $this->isByok;
        }

        return [...$data, ...$this->extras];
    }
}
