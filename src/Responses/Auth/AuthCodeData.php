<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Auth;

/**
 * @phpstan-type AuthCodeDataType array{
 *     id: string,
 *     app_id: int,
 *     created_at: string,
 * }
 */
final class AuthCodeData
{
    private function __construct(
        public readonly string $id,
        public readonly int $appId,
        public readonly string $createdAt,
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            id: is_string($attributes['id'] ?? null) ? $attributes['id'] : '',
            appId: is_int($attributes['app_id'] ?? null) ? $attributes['app_id'] : 0,
            createdAt: is_string($attributes['created_at'] ?? null) ? $attributes['created_at'] : '',
        );
    }

    /**
     * @return array{id: string, app_id: int, created_at: string}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'app_id' => $this->appId,
            'created_at' => $this->createdAt,
        ];
    }
}
