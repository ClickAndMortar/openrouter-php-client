<?php

declare(strict_types=1);

namespace OpenRouter\Contracts\Resources;

use OpenRouter\Responses\Presets\ListPresetsResponse;
use OpenRouter\Responses\Presets\ListPresetVersionsResponse;
use OpenRouter\Responses\Presets\PresetResponse;
use OpenRouter\Responses\Presets\PresetVersionResponse;
use OpenRouter\ValueObjects\Chat\CreateChatRequest;
use OpenRouter\ValueObjects\Messages\CreateMessagesRequest;
use OpenRouter\ValueObjects\Responses\CreateResponseRequest;

interface PresetsContract
{
    public function list(?int $limit = null, ?int $offset = null): ListPresetsResponse;

    public function retrieve(string $slug): PresetResponse;

    public function listVersions(string $slug, ?int $limit = null, ?int $offset = null): ListPresetVersionsResponse;

    public function retrieveVersion(string $slug, int|string $version): PresetVersionResponse;

    /**
     * Records a chat-completions request as a new version of the preset.
     *
     * @param  CreateChatRequest|array<string, mixed>  $parameters
     */
    public function createFromChat(string $slug, CreateChatRequest|array $parameters): PresetResponse;

    /**
     * Records a messages request as a new version of the preset.
     *
     * @param  CreateMessagesRequest|array<string, mixed>  $parameters
     */
    public function createFromMessages(string $slug, CreateMessagesRequest|array $parameters): PresetResponse;

    /**
     * Records a responses request as a new version of the preset.
     *
     * @param  CreateResponseRequest|array<string, mixed>  $parameters
     */
    public function createFromResponses(string $slug, CreateResponseRequest|array $parameters): PresetResponse;
}
