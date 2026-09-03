<?php

declare(strict_types=1);

namespace OpenRouter\Resources;

use OpenRouter\Contracts\Resources\PresetsContract;
use OpenRouter\Responses\Presets\ListPresetsResponse;
use OpenRouter\Responses\Presets\ListPresetVersionsResponse;
use OpenRouter\Responses\Presets\PresetResponse;
use OpenRouter\Responses\Presets\PresetVersionResponse;
use OpenRouter\ValueObjects\Chat\CreateChatRequest;
use OpenRouter\ValueObjects\Messages\CreateMessagesRequest;
use OpenRouter\ValueObjects\Responses\CreateResponseRequest;
use OpenRouter\ValueObjects\Transporter\Payload;

final class Presets implements PresetsContract
{
    use Concerns\Paginates;
    use Concerns\Transportable;

    /**
     * @see https://openrouter.ai/docs/api-reference/presets
     */
    public function list(?int $limit = null, ?int $offset = null): ListPresetsResponse
    {
        $response = $this->transporter->requestObject(
            Payload::list('presets', self::page($limit, $offset)),
        );

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return ListPresetsResponse::from($data, $response->meta());
    }

    public function retrieve(string $slug): PresetResponse
    {
        $response = $this->transporter->requestObject(Payload::retrieve('presets', $slug));

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return PresetResponse::from($data, $response->meta());
    }

    public function listVersions(string $slug, ?int $limit = null, ?int $offset = null): ListPresetVersionsResponse
    {
        $payload = Payload::retrieve('presets', $slug, '/versions', self::page($limit, $offset));

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return ListPresetVersionsResponse::from($data, $response->meta());
    }

    public function retrieveVersion(string $slug, int|string $version): PresetVersionResponse
    {
        $payload = Payload::retrieve('presets', $slug, '/versions/'.$version);

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return PresetVersionResponse::from($data, $response->meta());
    }

    /**
     * @param  CreateChatRequest|array<string, mixed>  $parameters
     */
    public function createFromChat(string $slug, CreateChatRequest|array $parameters): PresetResponse
    {
        $params = $parameters instanceof CreateChatRequest ? $parameters->toArray() : $parameters;

        return $this->createFrom("presets/{$slug}/chat/completions", $params);
    }

    /**
     * @param  CreateMessagesRequest|array<string, mixed>  $parameters
     */
    public function createFromMessages(string $slug, CreateMessagesRequest|array $parameters): PresetResponse
    {
        $params = $parameters instanceof CreateMessagesRequest ? $parameters->toArray() : $parameters;

        return $this->createFrom("presets/{$slug}/messages", $params);
    }

    /**
     * @param  CreateResponseRequest|array<string, mixed>  $parameters
     */
    public function createFromResponses(string $slug, CreateResponseRequest|array $parameters): PresetResponse
    {
        $params = $parameters instanceof CreateResponseRequest ? $parameters->toArray() : $parameters;

        return $this->createFrom("presets/{$slug}/responses", $params);
    }

    /**
     * @param  array<string, mixed>  $params
     */
    private function createFrom(string $resource, array $params): PresetResponse
    {
        $response = $this->transporter->requestObject(Payload::create($resource, $params));

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return PresetResponse::from($data, $response->meta());
    }
}
