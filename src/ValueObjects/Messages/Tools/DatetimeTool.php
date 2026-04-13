<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Messages\Tools;

/**
 * OpenRouter datetime server tool (`type: openrouter:datetime`). Returns the
 * current date/time in an optional `timezone`.
 */
final class DatetimeTool implements MessagesTool
{
    public function __construct(
        public readonly ?string $timezone = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $params = is_array($attributes['parameters'] ?? null) ? $attributes['parameters'] : [];

        return new self(
            timezone: isset($params['timezone']) && is_string($params['timezone']) ? $params['timezone'] : null,
        );
    }

    public function type(): string
    {
        return 'openrouter:datetime';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['type' => $this->type()];

        if ($this->timezone !== null) {
            $data['parameters'] = ['timezone' => $this->timezone];
        }

        return $data;
    }
}
