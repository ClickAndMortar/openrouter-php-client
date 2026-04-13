<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Tools;

/**
 * OpenRouter built-in datetime server tool. Optional `timezone` parameter
 * (IANA TZ string like `Europe/Paris`).
 */
final class DatetimeServerTool implements Tool
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
