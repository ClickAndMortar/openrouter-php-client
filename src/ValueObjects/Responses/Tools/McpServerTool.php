<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Responses\Tools;

use OpenRouter\Exceptions\InvalidArgumentException;

/**
 * Model Context Protocol (MCP) server tool. `server_label` is required.
 * `allowed_tools` and `require_approval` are passed through as opaque
 * arrays/strings since the spec models them as discriminated unions.
 */
final class McpServerTool implements Tool
{
    /**
     * @param  array<string, string>|null  $headers
     * @param  array<string, mixed>|list<string>|null  $allowedTools
     * @param  array<string, mixed>|string|null  $requireApproval
     */
    public function __construct(
        public readonly string $serverLabel,
        public readonly ?string $serverUrl = null,
        public readonly ?string $serverDescription = null,
        public readonly ?string $authorization = null,
        public readonly ?array $headers = null,
        public readonly ?string $connectorId = null,
        public readonly array|null $allowedTools = null,
        public readonly array|string|null $requireApproval = null,
    ) {
        if ($this->serverLabel === '') {
            throw new InvalidArgumentException('McpServerTool::$serverLabel must not be empty');
        }
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            serverLabel: is_string($attributes['server_label'] ?? null) ? $attributes['server_label'] : '',
            serverUrl: isset($attributes['server_url']) && is_string($attributes['server_url']) ? $attributes['server_url'] : null,
            serverDescription: isset($attributes['server_description']) && is_string($attributes['server_description']) ? $attributes['server_description'] : null,
            authorization: isset($attributes['authorization']) && is_string($attributes['authorization']) ? $attributes['authorization'] : null,
            headers: isset($attributes['headers']) && is_array($attributes['headers']) ? $attributes['headers'] : null,
            connectorId: isset($attributes['connector_id']) && is_string($attributes['connector_id']) ? $attributes['connector_id'] : null,
            allowedTools: isset($attributes['allowed_tools']) && is_array($attributes['allowed_tools']) ? $attributes['allowed_tools'] : null,
            requireApproval: $attributes['require_approval'] ?? null,
        );
    }

    public function type(): string
    {
        return 'mcp';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'type' => $this->type(),
            'server_label' => $this->serverLabel,
        ];

        if ($this->serverUrl !== null) {
            $data['server_url'] = $this->serverUrl;
        }
        if ($this->serverDescription !== null) {
            $data['server_description'] = $this->serverDescription;
        }
        if ($this->authorization !== null) {
            $data['authorization'] = $this->authorization;
        }
        if ($this->headers !== null) {
            $data['headers'] = $this->headers;
        }
        if ($this->connectorId !== null) {
            $data['connector_id'] = $this->connectorId;
        }
        if ($this->allowedTools !== null) {
            $data['allowed_tools'] = $this->allowedTools;
        }
        if ($this->requireApproval !== null) {
            $data['require_approval'] = $this->requireApproval;
        }

        return $data;
    }
}
