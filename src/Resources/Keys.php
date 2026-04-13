<?php

declare(strict_types=1);

namespace OpenRouter\Resources;

use OpenRouter\Contracts\Resources\KeysContract;
use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\Responses\Keys\CreateKeyResponse;
use OpenRouter\Responses\Keys\CurrentKeyResponse;
use OpenRouter\Responses\Keys\DeleteKeyResponse;
use OpenRouter\Responses\Keys\ListKeysResponse;
use OpenRouter\Responses\Keys\RetrieveKeyResponse;
use OpenRouter\ValueObjects\Keys\CreateKeyRequest;
use OpenRouter\ValueObjects\Keys\UpdateKeyRequest;
use OpenRouter\ValueObjects\Transporter\Payload;

final class Keys implements KeysContract
{
    use Concerns\Transportable;

    public function current(): CurrentKeyResponse
    {
        $payload = Payload::list('key');

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return CurrentKeyResponse::from($data, $response->meta());
    }

    public function list(?bool $includeDisabled = null, ?int $offset = null): ListKeysResponse
    {
        $query = [];
        if ($includeDisabled !== null) {
            $query['include_disabled'] = $includeDisabled ? 'true' : 'false';
        }
        if ($offset !== null) {
            if ($offset < 0) {
                throw new InvalidArgumentException('Keys::list()::$offset must be >= 0');
            }
            $query['offset'] = $offset;
        }

        $payload = Payload::list('keys', $query);

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return ListKeysResponse::from($data, $response->meta());
    }

    /**
     * @param  CreateKeyRequest|array<string, mixed>  $parameters
     */
    public function create(CreateKeyRequest|array $parameters): CreateKeyResponse
    {
        $params = $parameters instanceof CreateKeyRequest
            ? $parameters->toArray()
            : $parameters;

        $payload = Payload::create('keys', $params);

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return CreateKeyResponse::from($data, $response->meta());
    }

    public function retrieve(string $hash): RetrieveKeyResponse
    {
        if ($hash === '') {
            throw new InvalidArgumentException('Keys::retrieve()::$hash must not be an empty string');
        }

        $payload = Payload::retrieve('keys', $hash);

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return RetrieveKeyResponse::from($data, $response->meta());
    }

    public function delete(string $hash): DeleteKeyResponse
    {
        if ($hash === '') {
            throw new InvalidArgumentException('Keys::delete()::$hash must not be an empty string');
        }

        $payload = Payload::delete('keys', $hash);

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return DeleteKeyResponse::from($data, $response->meta());
    }

    /**
     * @param  UpdateKeyRequest|array<string, mixed>  $parameters
     */
    public function update(string $hash, UpdateKeyRequest|array $parameters): RetrieveKeyResponse
    {
        if ($hash === '') {
            throw new InvalidArgumentException('Keys::update()::$hash must not be an empty string');
        }

        $params = $parameters instanceof UpdateKeyRequest
            ? $parameters->toArray()
            : $parameters;

        $payload = Payload::modify('keys', $hash, $params);

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return RetrieveKeyResponse::from($data, $response->meta());
    }
}
