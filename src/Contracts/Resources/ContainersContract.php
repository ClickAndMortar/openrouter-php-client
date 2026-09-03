<?php

declare(strict_types=1);

namespace OpenRouter\Contracts\Resources;

use OpenRouter\Responses\BinaryResponse;
use OpenRouter\Responses\Containers\ContainerFileResponse;
use OpenRouter\Responses\Containers\ListContainerFilesResponse;
use OpenRouter\Responses\Files\FileResponse;

interface ContainersContract
{
    public function listFiles(string $containerId, ?int $limit = null, ?string $after = null): ListContainerFilesResponse;

    public function retrieveFile(string $containerId, string $fileId): ContainerFileResponse;

    public function downloadFile(string $containerId, string $fileId): BinaryResponse;

    /**
     * Copies a container file into workspace documents, where it outlives the
     * container and can be referenced like any other stored file.
     */
    public function promoteFile(string $containerId, string $fileId): FileResponse;
}
