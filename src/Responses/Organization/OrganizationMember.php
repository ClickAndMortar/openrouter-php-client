<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Organization;

/**
 * A single organization member record returned by `GET /organization/members`.
 *
 * @phpstan-type OrganizationMemberType array{
 *     id: string,
 *     email: string,
 *     first_name: ?string,
 *     last_name: ?string,
 *     role: string,
 * }
 */
final class OrganizationMember
{
    private function __construct(
        public readonly string $id,
        public readonly string $email,
        public readonly ?string $firstName,
        public readonly ?string $lastName,
        public readonly string $role,
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
            email: $str($attributes['email'] ?? null),
            firstName: $nullableStr($attributes['first_name'] ?? null),
            lastName: $nullableStr($attributes['last_name'] ?? null),
            role: $str($attributes['role'] ?? null),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'role' => $this->role,
        ];
    }
}
