<?php

declare(strict_types=1);

namespace OpenRouter\Contracts\Resources;

use OpenRouter\Responses\BinaryResponse;
use OpenRouter\Responses\Files\DeleteFileResponse;
use OpenRouter\Responses\Files\FileResponse;
use OpenRouter\Responses\Files\ListFilesResponse;
use OpenRouter\ValueObjects\Transporter\UploadedFile;

interface FilesContract
{
    /**
     * Lists stored files.
     *
     * `$filters` accepts the remaining documented query parameters: `after`,
     * `after_id`, `before_id` and `order`.
     *
     * @param  array<string, scalar|null>  $filters
     */
    public function list(
        ?int $limit = null,
        ?string $cursor = null,
        ?string $provider = null,
        ?string $workspaceId = null,
        array $filters = [],
    ): ListFilesResponse;

    /**
     * Uploads a file. Omit `$provider` to store it on OpenRouter; name one to
     * store it with that provider under your own key.
     */
    public function upload(
        UploadedFile $file,
        ?string $provider = null,
        ?string $workspaceId = null,
    ): FileResponse;

    public function retrieve(string $fileId, ?string $provider = null, ?string $workspaceId = null): FileResponse;

    public function delete(string $fileId, ?string $provider = null, ?string $workspaceId = null): DeleteFileResponse;

    /**
     * Downloads the file's bytes. Only files whose metadata reports
     * `downloadable` can be fetched.
     */
    public function download(string $fileId, ?string $provider = null, ?string $workspaceId = null): BinaryResponse;
}
