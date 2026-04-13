<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Transporter;

use OpenRouter\Enums\Transporter\ContentType;
use OpenRouter\ValueObjects\ApiKey;

/**
 * @internal
 */
final class Headers
{
    /**
     * @param  array<string, string>  $headers
     */
    private function __construct(private readonly array $headers)
    {
    }

    public static function create(): self
    {
        return new self([]);
    }

    public static function withAuthorization(ApiKey $apiKey): self
    {
        return new self([
            'Authorization' => "Bearer {$apiKey->toString()}",
        ]);
    }

    public function withContentType(ContentType $contentType, string $suffix = ''): self
    {
        return new self([
            ...$this->headers,
            'Content-Type' => $contentType->value.$suffix,
        ]);
    }

    public function withCustomHeader(string $name, string $value): self
    {
        return new self([
            ...$this->headers,
            $name => $value,
        ]);
    }

    /**
     * Sets the HTTP-Referer header. Identifies the application making the request in
     * OpenRouter's rankings dashboard.
     */
    public function withHttpReferer(string $referer): self
    {
        return new self([
            ...$this->headers,
            'HTTP-Referer' => $referer,
        ]);
    }

    /**
     * Sets the X-Title header. Sets the display name of the application in OpenRouter's dashboard.
     */
    public function withAppTitle(string $title): self
    {
        return new self([
            ...$this->headers,
            'X-Title' => $title,
        ]);
    }

    /**
     * Sets the X-OpenRouter-Categories header. Associates the application with OpenRouter
     * marketplace categories (e.g. "cli-agent,cloud-agent").
     */
    public function withAppCategories(string $categories): self
    {
        return new self([
            ...$this->headers,
            'X-OpenRouter-Categories' => $categories,
        ]);
    }

    /**
     * Sets the x-session-id header. Used by OpenRouter to group related requests.
     * Note: when a request body also carries `session_id`, the body value wins.
     */
    public function withSessionId(string $sessionId): self
    {
        return new self([
            ...$this->headers,
            'x-session-id' => $sessionId,
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return $this->headers;
    }
}
