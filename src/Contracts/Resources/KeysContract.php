<?php

declare(strict_types=1);

namespace OpenRouter\Contracts\Resources;

use OpenRouter\Responses\Keys\CreateKeyResponse;
use OpenRouter\Responses\Keys\CurrentKeyResponse;
use OpenRouter\Responses\Keys\DeleteKeyResponse;
use OpenRouter\Responses\Keys\ListKeysResponse;
use OpenRouter\Responses\Keys\RetrieveKeyResponse;
use OpenRouter\ValueObjects\Keys\CreateKeyRequest;
use OpenRouter\ValueObjects\Keys\UpdateKeyRequest;

interface KeysContract
{
    /**
     * Returns information on the API key backing the current request via
     * `GET /key`.
     */
    public function current(): CurrentKeyResponse;

    /**
     * Lists all API keys for the authenticated user via `GET /keys`.
     * Management key required.
     */
    public function list(?bool $includeDisabled = null, ?int $offset = null): ListKeysResponse;

    /**
     * Creates a new API key via `POST /keys`. Management key required.
     *
     * @param  CreateKeyRequest|array<string, mixed>  $parameters
     */
    public function create(CreateKeyRequest|array $parameters): CreateKeyResponse;

    /**
     * Retrieves a single API key by hash via `GET /keys/{hash}`.
     * Management key required.
     */
    public function retrieve(string $hash): RetrieveKeyResponse;

    /**
     * Deletes an API key by hash via `DELETE /keys/{hash}`.
     * Management key required.
     */
    public function delete(string $hash): DeleteKeyResponse;

    /**
     * Updates an API key by hash via `PATCH /keys/{hash}`.
     * Management key required.
     *
     * @param  UpdateKeyRequest|array<string, mixed>  $parameters
     */
    public function update(string $hash, UpdateKeyRequest|array $parameters): RetrieveKeyResponse;
}
