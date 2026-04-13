<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Plugins;

/**
 * File-parser plugin. `pdf` is an opaque options object (e.g.
 * `['engine' => 'cloudflare-ai']`).
 */
final class FileParserPlugin implements Plugin
{
    /**
     * @param  array<string, mixed>|null  $pdf
     */
    public function __construct(
        public readonly ?bool $enabled = null,
        public readonly ?array $pdf = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            enabled: isset($attributes['enabled']) ? (bool) $attributes['enabled'] : null,
            pdf: isset($attributes['pdf']) && is_array($attributes['pdf']) ? $attributes['pdf'] : null,
        );
    }

    public function id(): string
    {
        return 'file-parser';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['id' => $this->id()];

        if ($this->enabled !== null) {
            $data['enabled'] = $this->enabled;
        }
        if ($this->pdf !== null) {
            $data['pdf'] = $this->pdf;
        }

        return $data;
    }
}
