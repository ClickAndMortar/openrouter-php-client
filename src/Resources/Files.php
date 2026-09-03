<?php

declare(strict_types=1);

namespace OpenRouter\Resources;

use OpenRouter\Contracts\Resources\FilesContract;
use OpenRouter\Responses\BinaryResponse;
use OpenRouter\Responses\Files\DeleteFileResponse;
use OpenRouter\Responses\Files\FileResponse;
use OpenRouter\Responses\Files\ListFilesResponse;
use OpenRouter\ValueObjects\Transporter\Payload;
use OpenRouter\ValueObjects\Transporter\UploadedFile;

final class Files implements FilesContract
{
    use Concerns\Transportable;

    /**
     * @see https://openrouter.ai/docs/api-reference/files
     *
     * @param  array<string, scalar|null>  $filters
     */
    public function list(
        ?int $limit = null,
        ?string $cursor = null,
        ?string $provider = null,
        ?string $workspaceId = null,
        array $filters = [],
    ): ListFilesResponse {
        $payload = Payload::list('files', self::query([
            'limit' => $limit,
            'cursor' => $cursor,
            'provider' => $provider,
            'workspace_id' => $workspaceId,
            ...$filters,
        ]));

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return ListFilesResponse::from($data, $response->meta());
    }

    public function upload(
        UploadedFile $file,
        ?string $provider = null,
        ?string $workspaceId = null,
    ): FileResponse {
        $payload = Payload::upload(
            'files',
            ['file' => $file],
            self::query(['provider' => $provider, 'workspace_id' => $workspaceId]),
        );

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return FileResponse::from($data, $response->meta());
    }

    public function retrieve(string $fileId, ?string $provider = null, ?string $workspaceId = null): FileResponse
    {
        $payload = Payload::retrieve(
            'files',
            $fileId,
            parameters: self::query(['provider' => $provider, 'workspace_id' => $workspaceId]),
        );

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return FileResponse::from($data, $response->meta());
    }

    public function delete(string $fileId, ?string $provider = null, ?string $workspaceId = null): DeleteFileResponse
    {
        $payload = Payload::delete(
            'files',
            $fileId,
            query: self::query(['provider' => $provider, 'workspace_id' => $workspaceId]),
        );

        $response = $this->transporter->requestObject($payload);

        /** @var array<string, mixed> $data */
        $data = $response->data();

        return DeleteFileResponse::from($data, $response->meta());
    }

    public function download(string $fileId, ?string $provider = null, ?string $workspaceId = null): BinaryResponse
    {
        $payload = Payload::retrieve(
            'files',
            $fileId,
            '/content',
            self::query(['provider' => $provider, 'workspace_id' => $workspaceId]),
        );

        return $this->transporter->requestContent($payload);
    }

    /**
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    private static function query(array $params): array
    {
        return array_filter($params, static fn (mixed $value): bool => $value !== null);
    }
}
