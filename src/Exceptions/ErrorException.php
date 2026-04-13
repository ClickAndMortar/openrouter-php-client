<?php

declare(strict_types=1);

namespace OpenRouter\Exceptions;

use Exception;
use Psr\Http\Message\ResponseInterface;

/**
 * Base class for OpenRouter HTTP error responses. Subclasses under
 * {@see \OpenRouter\Exceptions\Http} specialize per-status (400, 401, ...).
 * Catching `ErrorException` still catches every variant — preserves V1.1 BC.
 */
class ErrorException extends Exception
{
    private readonly int $statusCode;

    /** @var array<string, mixed>|null */
    private readonly ?array $metadata;

    /**
     * @param  array{message?: string|array<int, string>, type?: ?string, code?: string|int|null, metadata?: array<string, mixed>}|string  $contents
     */
    public function __construct(
        private readonly string|array $contents,
        public readonly ResponseInterface $response,
    ) {
        $this->statusCode = $response->getStatusCode();

        $contents = is_string($contents) ? ['message' => $contents] : $contents;
        $message = ($contents['message'] ?? null) ?: (string) ($contents['code'] ?? null) ?: 'Unknown error';

        if (is_array($message)) {
            $message = implode(PHP_EOL, $message);
        }

        $this->metadata = isset($this->contents['metadata']) && is_array($this->contents['metadata'])
            ? $this->contents['metadata']
            : null;

        parent::__construct($message);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrorMessage(): string
    {
        return $this->getMessage();
    }

    public function getErrorType(): ?string
    {
        return is_array($this->contents) ? ($this->contents['type'] ?? null) : null;
    }

    public function getErrorCode(): string|int|null
    {
        return is_array($this->contents) ? ($this->contents['code'] ?? null) : null;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getMetadata(): ?array
    {
        return $this->metadata;
    }
}
