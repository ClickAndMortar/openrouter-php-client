<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Organization;

/**
 * The scope grant attached to a newly created customer organization.
 *
 * `$scopes` is an open list upstream, so it is exposed as plain strings rather
 * than an enum — a scope this SDK does not know still reaches the caller.
 */
final class OrganizationGrant
{
    /**
     * @param  list<string>  $scopes
     * @param  array<string, mixed>  $extras
     */
    private function __construct(
        public readonly string $id,
        public readonly array $scopes,
        public readonly array $extras = [],
    ) {
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        $scopes = [];

        if (is_array($attributes['scopes'] ?? null)) {
            foreach ($attributes['scopes'] as $scope) {
                if (is_string($scope)) {
                    $scopes[] = $scope;
                }
            }
        }

        return new self(
            id: is_string($attributes['id'] ?? null) ? $attributes['id'] : '',
            scopes: $scopes,
            extras: array_diff_key($attributes, array_flip(['id', 'scopes'])),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'scopes' => $this->scopes,
            ...$this->extras,
        ];
    }
}
