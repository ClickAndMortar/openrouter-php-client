<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Guardrails;

use OpenRouter\Enums\Guardrails\GuardrailInterval;

/**
 * Typed builder for `PATCH /guardrails/{id}`. All fields are optional.
 */
final class UpdateGuardrailRequest
{
    /**
     * @param  list<string>|null  $allowedModels
     * @param  list<string>|null  $allowedProviders
     * @param  list<string>|null  $ignoredProviders
     * @param  array<string, mixed>  $extras
     */
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $description = null,
        public readonly ?float $limitUsd = null,
        public readonly GuardrailInterval|string|null $resetInterval = null,
        public readonly ?bool $enforceZdr = null,
        public readonly ?array $allowedModels = null,
        public readonly ?array $allowedProviders = null,
        public readonly ?array $ignoredProviders = null,
        public readonly array $extras = [],
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];

        if ($this->name !== null) {
            $data['name'] = $this->name;
        }

        if ($this->description !== null) {
            $data['description'] = $this->description;
        }

        if ($this->limitUsd !== null) {
            $data['limit_usd'] = $this->limitUsd;
        }

        if ($this->resetInterval !== null) {
            $data['reset_interval'] = $this->resetInterval instanceof GuardrailInterval
                ? $this->resetInterval->value
                : $this->resetInterval;
        }

        if ($this->enforceZdr !== null) {
            $data['enforce_zdr'] = $this->enforceZdr;
        }

        if ($this->allowedModels !== null) {
            $data['allowed_models'] = $this->allowedModels;
        }

        if ($this->allowedProviders !== null) {
            $data['allowed_providers'] = $this->allowedProviders;
        }

        if ($this->ignoredProviders !== null) {
            $data['ignored_providers'] = $this->ignoredProviders;
        }

        foreach ($this->extras as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }
}
