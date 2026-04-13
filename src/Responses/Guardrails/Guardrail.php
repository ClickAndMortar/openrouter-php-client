<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Guardrails;

/**
 * Shared representation of a single guardrail record as returned by
 * `/guardrails`, `/guardrails/{id}`, and as `data` on create/update responses.
 */
final class Guardrail
{
    /**
     * @param  list<string>|null  $allowedModels
     * @param  list<string>|null  $allowedProviders
     * @param  list<string>|null  $ignoredProviders
     */
    private function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $createdAt,
        public readonly ?string $description,
        public readonly ?float $limitUsd,
        public readonly ?string $resetInterval,
        public readonly ?bool $enforceZdr,
        public readonly ?array $allowedModels,
        public readonly ?array $allowedProviders,
        public readonly ?array $ignoredProviders,
        public readonly ?string $updatedAt,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $str = static fn (mixed $v): string => is_string($v) ? $v : '';
        $nullableStr = static fn (mixed $v): ?string => is_string($v) ? $v : null;
        $nullableFloat = static fn (mixed $v): ?float => is_numeric($v) ? (float) $v : null;
        $nullableBool = static fn (mixed $v): ?bool => is_bool($v) ? $v : null;
        $stringList = static function (mixed $v): ?array {
            if (! is_array($v)) {
                return null;
            }
            $out = [];
            foreach ($v as $item) {
                if (is_string($item)) {
                    $out[] = $item;
                }
            }

            return $out;
        };

        return new self(
            id: $str($attributes['id'] ?? null),
            name: $str($attributes['name'] ?? null),
            createdAt: $str($attributes['created_at'] ?? null),
            description: $nullableStr($attributes['description'] ?? null),
            limitUsd: $nullableFloat($attributes['limit_usd'] ?? null),
            resetInterval: $nullableStr($attributes['reset_interval'] ?? null),
            enforceZdr: $nullableBool($attributes['enforce_zdr'] ?? null),
            allowedModels: $stringList($attributes['allowed_models'] ?? null),
            allowedProviders: $stringList($attributes['allowed_providers'] ?? null),
            ignoredProviders: $stringList($attributes['ignored_providers'] ?? null),
            updatedAt: $nullableStr($attributes['updated_at'] ?? null),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->createdAt,
            'description' => $this->description,
            'limit_usd' => $this->limitUsd,
            'reset_interval' => $this->resetInterval,
            'enforce_zdr' => $this->enforceZdr,
            'allowed_models' => $this->allowedModels,
            'allowed_providers' => $this->allowedProviders,
            'ignored_providers' => $this->ignoredProviders,
            'updated_at' => $this->updatedAt,
        ];
    }
}
