<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Tools;

/**
 * Apply-patch server tool — no parameters.
 */
final class ApplyPatchServerTool implements Tool
{
    public function __construct()
    {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self();
    }

    public function type(): string
    {
        return 'apply_patch';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return ['type' => $this->type()];
    }
}
