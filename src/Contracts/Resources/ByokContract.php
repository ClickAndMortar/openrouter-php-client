<?php

declare(strict_types=1);

namespace OpenRouter\Contracts\Resources;

use OpenRouter\Responses\Byok\ByokKeyResponse;
use OpenRouter\Responses\Byok\DeleteByokKeyResponse;
use OpenRouter\Responses\Byok\ListByokKeysResponse;
use OpenRouter\ValueObjects\Byok\CreateByokKeyRequest;
use OpenRouter\ValueObjects\Byok\UpdateByokKeyRequest;

interface ByokContract
{
    public function list(
        ?int $limit = null,
        ?int $offset = null,
        ?string $workspaceId = null,
        ?string $provider = null,
    ): ListByokKeysResponse;

    /**
     * @param  CreateByokKeyRequest|array<string, mixed>  $parameters
     */
    public function create(CreateByokKeyRequest|array $parameters): ByokKeyResponse;

    public function retrieve(string $id): ByokKeyResponse;

    /**
     * @param  UpdateByokKeyRequest|array<string, mixed>  $parameters
     */
    public function update(string $id, UpdateByokKeyRequest|array $parameters): ByokKeyResponse;

    public function delete(string $id): DeleteByokKeyResponse;
}
