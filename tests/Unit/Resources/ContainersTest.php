<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Resources;

use OpenRouter\Factory;
use OpenRouter\Responses\BinaryResponse;
use OpenRouter\Responses\Containers\ContainerFileResponse;
use OpenRouter\Responses\Containers\ListContainerFilesResponse;
use OpenRouter\Responses\Files\FileResponse;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use OpenRouter\Tests\Fixtures\ContainerFileRetrieveFixture;
use OpenRouter\Tests\Fixtures\ContainerFilesListFixture;
use OpenRouter\Tests\Fixtures\FilesRetrieveFixture;
use PHPUnit\Framework\TestCase;

final class ContainersTest extends TestCase
{
    private function client(RecordingHttpClient $http): \OpenRouter\Client
    {
        return (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();
    }

    public function testListFilesHitsTheContainerFilesEndpoint(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ContainerFilesListFixture::ATTRIBUTES);

        $response = $this->client($http)->containers()->listFiles('cntr_1', limit: 10, after: 'cfile_9');

        $request = $http->lastRequest();
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringContainsString('/containers/cntr_1/files', (string) $request->getUri());

        $query = [];
        parse_str($request->getUri()->getQuery(), $query);
        $this->assertSame('10', $query['limit']);
        $this->assertSame('cfile_9', $query['after']);

        $this->assertInstanceOf(ListContainerFilesResponse::class, $response);
        $this->assertFalse($response->hasMore);
        $this->assertCount(1, $response->data);
        $this->assertSame('cfile_682e0e8a43c88191a7978f477a09bdf5', $response->data[0]->id);
        $this->assertSame('cntr_682e30645a488191b6363d0b9b992d3a', $response->data[0]->containerId);
        $this->assertSame(880, $response->data[0]->bytes);
        $this->assertSame('assistant', $response->data[0]->source);
    }

    public function testRetrieveFileHitsTheFileEndpoint(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ContainerFileRetrieveFixture::ATTRIBUTES);

        $response = $this->client($http)->containers()->retrieveFile('cntr_1', 'cfile_1');

        $this->assertStringEndsWith('/containers/cntr_1/files/cfile_1', (string) $http->lastRequest()->getUri());
        $this->assertInstanceOf(ContainerFileResponse::class, $response);
        $this->assertSame('/mnt/data/88e12fa4-6c64-4725-ab63-695e85602e73.png', $response->data->path);
        $this->assertSame(1747848842, $response->data->createdAt);
    }

    public function testDownloadFileReturnsRawBytes(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueBinary("\x89PNG\r\n", 'image/png');

        $response = $this->client($http)->containers()->downloadFile('cntr_1', 'cfile_1');

        $this->assertStringEndsWith('/containers/cntr_1/files/cfile_1/content', (string) $http->lastRequest()->getUri());
        $this->assertInstanceOf(BinaryResponse::class, $response);
        $this->assertSame("\x89PNG\r\n", $response->contents);
    }

    public function testPromoteFilePostsAndReturnsAStoredFile(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(FilesRetrieveFixture::ATTRIBUTES);

        $response = $this->client($http)->containers()->promoteFile('cntr_1', 'cfile_1');

        $request = $http->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/containers/cntr_1/files/cfile_1/promote', (string) $request->getUri());
        $this->assertInstanceOf(FileResponse::class, $response);
        $this->assertSame('document.pdf', $response->data->filename);
    }

    public function testContainerFileKeepsUnknownFieldsInExtras(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson([...ContainerFileRetrieveFixture::ATTRIBUTES, 'a_new_field' => 7]);

        $response = $this->client($http)->containers()->retrieveFile('cntr_1', 'cfile_1');

        $this->assertSame(7, $response->data->extras['a_new_field']);
    }
}
