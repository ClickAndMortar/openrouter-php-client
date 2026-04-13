<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Plugins;

/**
 * Moderation plugin — no parameters.
 */
final class ModerationPlugin implements Plugin
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

    public function id(): string
    {
        return 'moderation';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return ['id' => $this->id()];
    }
}
