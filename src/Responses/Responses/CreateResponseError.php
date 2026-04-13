<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Responses;

/**
 * @phpstan-type CreateResponseErrorType array{code: string, message: string}
 */
final class CreateResponseError
{
    private function __construct(
        public readonly string $code,
        public readonly string $message,
    ) {
    }

    /**
     * @param  CreateResponseErrorType  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            code: $attributes['code'],
            message: $attributes['message'],
        );
    }

    /**
     * @return CreateResponseErrorType
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'message' => $this->message,
        ];
    }
}
