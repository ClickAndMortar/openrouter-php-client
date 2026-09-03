<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Resources;

use OpenRouter\Factory;
use OpenRouter\Responses\BinaryResponse;
use OpenRouter\Responses\Files\DeleteFileResponse;
use OpenRouter\Responses\Files\FileResponse;
use OpenRouter\Responses\Files\ListFilesResponse;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use OpenRouter\Tests\Fixtures\FilesDeleteFixture;
use OpenRouter\Tests\Fixtures\FilesListFixture;
use OpenRouter\Tests\Fixtures\FilesRetrieveFixture;
use OpenRouter\ValueObjects\Transporter\UploadedFile;
use PHPUnit\Framework\TestCase;

final class FilesTest extends TestCase
{
    private function client(RecordingHttpClient $http): \OpenRouter\Client
    {
        return (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();
    }

    public function testListHitsTheFilesEndpointAsGet(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(FilesListFixture::ATTRIBUTES);

        $this->client($http)->files()->list();

        $request = $http->lastRequest();
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/files', (string) $request->getUri());
    }

    public function testListPassesPaginationAndProviderFilters(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(FilesListFixture::ATTRIBUTES);

        $this->client($http)->files()->list(
            limit: 20,
            provider: 'openai',
            workspaceId: 'ws_1',
            filters: ['order' => 'desc', 'after_id' => 'or_file_9'],
        );

        $query = [];
        parse_str($http->lastRequest()->getUri()->getQuery(), $query);

        $this->assertSame('20', $query['limit']);
        $this->assertSame('openai', $query['provider']);
        $this->assertSame('ws_1', $query['workspace_id']);
        $this->assertSame('desc', $query['order']);
        $this->assertSame('or_file_9', $query['after_id']);
    }

    public function testListReturnsTypedFiles(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(FilesListFixture::ATTRIBUTES);

        $response = $this->client($http)->files()->list();

        $this->assertInstanceOf(ListFilesResponse::class, $response);
        $this->assertSame('openrouter', $response->shape);
        $this->assertFalse($response->hasMore);
        $this->assertNull($response->cursor);
        $this->assertCount(1, $response->data);

        $file = $response->data[0];
        $this->assertSame('or_file_011CNha8iCJcU1wXNR6q4V8w', $file->id);
        $this->assertSame('document.pdf', $file->filename);
        $this->assertSame('application/pdf', $file->mimeType);
        $this->assertSame(1024000, $file->sizeInBytes());
        $this->assertFalse($file->downloadable);
    }

    public function testUploadSendsAMultipartPost(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(FilesRetrieveFixture::ATTRIBUTES);

        $response = $this->client($http)->files()->upload(
            UploadedFile::fromString('%PDF-1.4', 'document.pdf', 'application/pdf'),
            provider: 'openai',
        );

        $request = $http->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringStartsWith('multipart/form-data;', $request->getHeaderLine('Content-Type'));
        $this->assertStringContainsString('filename="document.pdf"', (string) $request->getBody());
        $this->assertStringContainsString('provider=openai', (string) $request->getUri());

        $this->assertInstanceOf(FileResponse::class, $response);
        $this->assertSame('document.pdf', $response->data->filename);
    }

    public function testRetrieveHitsTheFileEndpoint(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(FilesRetrieveFixture::ATTRIBUTES);

        $response = $this->client($http)->files()->retrieve('or_file_1');

        $this->assertSame('GET', $http->lastRequest()->getMethod());
        $this->assertStringEndsWith('/files/or_file_1', (string) $http->lastRequest()->getUri());
        $this->assertSame('file', $response->data->type);
    }

    public function testDeleteHitsTheFileEndpointAsDelete(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(FilesDeleteFixture::ATTRIBUTES);

        $response = $this->client($http)->files()->delete('or_file_1');

        $this->assertSame('DELETE', $http->lastRequest()->getMethod());
        $this->assertStringEndsWith('/files/or_file_1', (string) $http->lastRequest()->getUri());
        $this->assertInstanceOf(DeleteFileResponse::class, $response);
        $this->assertSame('or_file_011CNha8iCJcU1wXNR6q4V8w', $response->id);
        $this->assertTrue($response->deleted);
    }

    public function testDeleteForwardsProviderAndWorkspace(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(FilesDeleteFixture::ATTRIBUTES);

        $this->client($http)->files()->delete('or_file_1', provider: 'openai', workspaceId: 'ws_1');

        $query = [];
        parse_str($http->lastRequest()->getUri()->getQuery(), $query);

        $this->assertSame('openai', $query['provider'] ?? null, 'a file stored with a provider must be deleted from that provider');
        $this->assertSame('ws_1', $query['workspace_id'] ?? null);
    }

    public function testDownloadReturnsRawBytes(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueBinary('%PDF-1.4 raw', 'application/pdf');

        $response = $this->client($http)->files()->download('or_file_1');

        $this->assertStringEndsWith('/files/or_file_1/content', (string) $http->lastRequest()->getUri());
        $this->assertInstanceOf(BinaryResponse::class, $response);
        $this->assertSame('%PDF-1.4 raw', $response->contents);
        $this->assertSame('application/pdf', $response->contentType);
    }

    public function testRetrieveAndDownloadForwardProviderAndWorkspace(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(FilesRetrieveFixture::ATTRIBUTES);
        $http->enqueueBinary('bytes', 'application/pdf');

        $files = $this->client($http)->files();
        $files->retrieve('or_file_1', provider: 'openai', workspaceId: 'ws_1');
        $retrieveQuery = $http->lastRequest()->getUri()->getQuery();

        $files->download('or_file_1', provider: 'openai', workspaceId: 'ws_1');
        $downloadQuery = $http->lastRequest()->getUri()->getQuery();

        foreach ([$retrieveQuery, $downloadQuery] as $raw) {
            $query = [];
            parse_str($raw, $query);
            $this->assertSame('openai', $query['provider'] ?? null);
            $this->assertSame('ws_1', $query['workspace_id'] ?? null);
        }
    }

    public function testStoredFileKeepsUnknownFieldsInExtras(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson([...FilesRetrieveFixture::ATTRIBUTES, 'a_new_field' => 'value']);

        $response = $this->client($http)->files()->retrieve('or_file_1');

        $this->assertSame('value', $response->data->extras['a_new_field']);
    }

    public function testStoredFileNormalisesTheOpenAiShape(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson([
            '_shape' => 'openai',
            'id' => 'file-abc',
            'object' => 'file',
            'bytes' => 2048,
            'created_at' => 1747848842,
            'filename' => 'notes.txt',
            'purpose' => 'assistants',
            'status' => 'processed',
        ]);

        $file = $this->client($http)->files()->retrieve('file-abc')->data;

        $this->assertSame('openai', $file->shape);
        $this->assertSame('notes.txt', $file->filename);
        $this->assertSame(2048, $file->sizeInBytes(), 'bytes is the OpenAI spelling of size_bytes');
        $this->assertSame('assistants', $file->purpose);
    }
}
