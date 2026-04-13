<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Keys;

use OpenRouter\Enums\Keys\LimitReset;

/**
 * Typed builder for `PATCH /keys/{hash}`. All fields are optional.
 */
final class UpdateKeyRequest
{
    /**
     * @param  array<string, mixed>  $extras
     */
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?bool $disabled = null,
        public readonly ?float $limit = null,
        public readonly LimitReset|string|null $limitReset = null,
        public readonly ?bool $includeByokInLimit = null,
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

        if ($this->disabled !== null) {
            $data['disabled'] = $this->disabled;
        }

        if ($this->limit !== null) {
            $data['limit'] = $this->limit;
        }

        if ($this->limitReset !== null) {
            $data['limit_reset'] = $this->limitReset instanceof LimitReset
                ? $this->limitReset->value
                : $this->limitReset;
        }

        if ($this->includeByokInLimit !== null) {
            $data['include_byok_in_limit'] = $this->includeByokInLimit;
        }

        foreach ($this->extras as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }
}
