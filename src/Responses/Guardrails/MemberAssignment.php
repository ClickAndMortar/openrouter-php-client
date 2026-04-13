<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Guardrails;

/**
 * Represents a single organization member-to-guardrail assignment row.
 */
final class MemberAssignment
{
    private function __construct(
        public readonly string $id,
        public readonly string $userId,
        public readonly string $organizationId,
        public readonly string $guardrailId,
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
            userId: $str($attributes['user_id'] ?? null),
            organizationId: $str($attributes['organization_id'] ?? null),
            guardrailId: $str($attributes['guardrail_id'] ?? null),
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
            'user_id' => $this->userId,
            'organization_id' => $this->organizationId,
            'guardrail_id' => $this->guardrailId,
            'assigned_by' => $this->assignedBy,
            'created_at' => $this->createdAt,
        ];
    }
}
