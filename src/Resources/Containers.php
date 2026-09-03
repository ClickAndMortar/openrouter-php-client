<?php

declare(strict_types=1);

namespace OpenRouter\Resources;

use OpenRouter\Contracts\Resources\ContainersContract;
use OpenRouter\Responses\BinaryResponse;
use OpenRouter\Responses\Containers\ContainerFileResponse;
use OpenRouter\Responses\Containers\ListContainerFilesResponse;
use OpenRouter\Responses\Files\FileResponse;
use OpenRouter\ValueObjects\Transporter\Payload;

final class Containers implements ContainersContract
{
    use Concerns\Transportable;

    public function listFiles(string $containerId, ?int $limit = null, ?string $after = null): ListContainerFilesResponse
    {
        $query = array_filter(
            ['limit' => $limit, 'after' => $after],
            static fn (mixed $value): bool => $value !== null,
        );

        $payload = Payload::retrieve('containers', $containerId, '/files', $query);

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return ListContainerFilesResponse::from($data, $response->meta());
    }

    public function retrieveFile(string $containerId, string $fileId): ContainerFileResponse
    {
        $payload = Payload::retrieve('containers', $containerId, "/files/{$fileId}");

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return ContainerFileResponse::from($data, $response->meta());
    }

    public function downloadFile(string $containerId, string $fileId): BinaryResponse
    {
        return $this->transporter->requestContent(
            Payload::retrieve('containers', $containerId, "/files/{$fileId}/content"),
        );
    }

    public function promoteFile(string $containerId, string $fileId): FileResponse
    {
        $payload = Payload::create("containers/{$containerId}/files/{$fileId}/promote", []);

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return FileResponse::from($data, $response->meta());
    }
}
