<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Tools;

use OpenRouter\Enums\Responses\Tools\ComputerEnvironment;
use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * Computer-use server tool (preview). Display dimensions and the target
 * environment are required.
 */
final class ComputerUseServerTool implements Tool
{
    public function __construct(
        public readonly int $displayWidth,
        public readonly int $displayHeight,
        public readonly ComputerEnvironment $environment,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $raw = is_string($attributes['environment'] ?? null) ? $attributes['environment'] : 'browser';
        $environment = ComputerEnvironment::tryFrom($raw);
        if ($environment === null) {
            throw new InvalidArgumentException(sprintf(
                'ComputerUseServerTool::$environment must be one of %s, got "%s"',
                implode('/', ComputerEnvironment::values()),
                $raw,
            ));
        }

        return new self(
            displayWidth: (int) ($attributes['display_width'] ?? 0),
            displayHeight: (int) ($attributes['display_height'] ?? 0),
            environment: $environment,
        );
    }

    public function type(): string
    {
        return 'computer_use_preview';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type(),
            'display_width' => $this->displayWidth,
            'display_height' => $this->displayHeight,
            'environment' => $this->environment->value,
        ];
    }
}
