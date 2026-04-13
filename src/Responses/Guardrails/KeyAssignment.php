<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Guardrails;

/**
 * Represents a single API-key-to-guardrail assignment row.
 */
final class KeyAssignment
{
    private function __construct(
        public readonly string $id,
        public readonly string $keyHash,
        public readonly string $guardrailId,
        public readonly string $keyName,
        public readonly string $keyLabel,
        public readonly ?string $assignedBy,
        public readonly string $createdAt,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $str = static fn (mixed $v): string => is_string($v) ? $v : '';
        $nullableStr = static fn (mixed $v): ?string => is_string($v) ? $v : null;

        return new self(
            id: $str($attributes['id'] ?? null),
            keyHash: $str($attributes['key_hash'] ?? null),
            guardrailId: $str($attributes['guardrail_id'] ?? null),
            keyName: $str($attributes['key_name'] ?? null),
            keyLabel: $str($attributes['key_label'] ?? null),
            assignedBy: $nullableStr($attributes['assigned_by'] ?? null),
            createdAt: $str($attributes['created_at'] ?? null),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'key_hash' => $this->keyHash,
            'guardrail_id' => $this->guardrailId,
            'key_name' => $this->keyName,
            'key_label' => $this->keyLabel,
            'assigned_by' => $this->assignedBy,
            'created_at' => $this->createdAt,
        ];
    }
}
