<?php

declare(strict_types=1);

namespace OpenRouter\Resources;

use OpenRouter\Contracts\Resources\ByokContract;
use OpenRouter\Responses\Byok\ByokKeyResponse;
use OpenRouter\Responses\Byok\DeleteByokKeyResponse;
use OpenRouter\Responses\Byok\ListByokKeysResponse;
use OpenRouter\ValueObjects\Byok\CreateByokKeyRequest;
use OpenRouter\ValueObjects\Byok\UpdateByokKeyRequest;
use OpenRouter\ValueObjects\Transporter\Payload;

final class Byok implements ByokContract
{
    use Concerns\Paginates;
    use Concerns\Transportable;

    /**
     * @see https://openrouter.ai/docs/api-reference/byok
     */
    public function list(
        ?int $limit = null,
        ?int $offset = null,
        ?string $workspaceId = null,
        ?string $provider = null,
    ): ListByokKeysResponse {
        $query = [
            ...self::page($limit, $offset),
            ...array_filter(
                ['workspace_id' => $workspaceId, 'provider' => $provider],
                static fn (?string $value): bool => $value !== null,
            ),
        ];

        $response = $this->transporter->requestObject(Payload::list('byok', $query));

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return ListByokKeysResponse::from($data, $response->meta());
    }

    /**
     * @param  CreateByokKeyRequest|array<string, mixed>  $parameters
     */
    public function create(CreateByokKeyRequest|array $parameters): ByokKeyResponse
    {
        $params = $parameters instanceof CreateByokKeyRequest ? $parameters->toArray() : $parameters;

        return $this->single(Payload::create('byok', $params));
    }

    public function retrieve(string $id): ByokKeyResponse
    {
        return $this->single(Payload::retrieve('byok', $id));
    }

    /**
     * @param  UpdateByokKeyRequest|array<string, mixed>  $parameters
     */
    public function update(string $id, UpdateByokKeyRequest|array $parameters): ByokKeyResponse
    {
        $params = $parameters instanceof UpdateByokKeyRequest ? $parameters->toArray() : $parameters;

        return $this->single(Payload::modify('byok', $id, $params));
    }

    public function delete(string $id): DeleteByokKeyResponse
    {
        $response = $this->transporter->requestObject(Payload::delete('byok', $id));

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return DeleteByokKeyResponse::from($data, $response->meta());
    }

    private function single(Payload $payload): ByokKeyResponse
    {
        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return ByokKeyResponse::from($data, $response->meta());
    }
}
