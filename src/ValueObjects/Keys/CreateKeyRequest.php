<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Keys;

use OpenRouter\Enums\Keys\LimitReset;
use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * Typed builder for `POST /keys`. Requires `name`; other fields are optional.
 */
final class CreateKeyRequest
{
    /**
     * @param  array<string, mixed>  $extras
     */
    public function __construct(
        public readonly string $name,
        public readonly ?float $limit = null,
        public readonly LimitReset|string|null $limitReset = null,
        public readonly ?string $expiresAt = null,
        public readonly ?bool $includeByokInLimit = null,
        public readonly ?string $creatorUserId = null,
        public readonly array $extras = [],
    ) {
        if ($this->name === '') {
            throw new InvalidArgumentException('CreateKeyRequest::$name must not be an empty string');
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = ['name' => $this->name];

        if ($this->limit !== null) {
            $data['limit'] = $this->limit;
        }

        if ($this->limitReset !== null) {
            $data['limit_reset'] = $this->limitReset instanceof LimitReset
                ? $this->limitReset->value
                : $this->limitReset;
        }

        if ($this->expiresAt !== null) {
            $data['expires_at'] = $this->expiresAt;
        }

        if ($this->includeByokInLimit !== null) {
            $data['include_byok_in_limit'] = $this->includeByokInLimit;
        }

        if ($this->creatorUserId !== null) {
            $data['creator_user_id'] = $this->creatorUserId;
        }

        foreach ($this->extras as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }
}
