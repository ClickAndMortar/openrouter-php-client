<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Resources;

use OpenRouter\Factory;
use OpenRouter\Responses\BinaryResponse;
use OpenRouter\Responses\Videos\ListVideoModelsResponse;
use OpenRouter\Responses\Videos\VideoJobResponse;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use OpenRouter\Tests\Fixtures\VideosJobFixture;
use OpenRouter\Tests\Fixtures\VideosModelsFixture;
use OpenRouter\ValueObjects\Videos\CreateVideoRequest;
use PHPUnit\Framework\TestCase;

final class VideosTest extends TestCase
{
    private function client(RecordingHttpClient $http): \OpenRouter\Client
    {
        return (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();
    }

    public function testGeneratePostsTheRequestBody(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(VideosJobFixture::ATTRIBUTES);

        $this->client($http)->videos()->generate(new CreateVideoRequest(
            model: 'google/veo-3',
            prompt: 'A cat surfing',
            duration: 8,
            aspectRatio: '16:9',
            generateAudio: true,
            seed: 42,
        ));

        $request = $http->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/videos', (string) $request->getUri());

        $body = json_decode((string) $request->getBody(), true);
        $this->assertSame('google/veo-3', $body['model']);
        $this->assertSame('A cat surfing', $body['prompt']);
        $this->assertSame(8, $body['duration']);
        $this->assertSame('16:9', $body['aspect_ratio']);
        $this->assertTrue($body['generate_audio']);
        $this->assertSame(42, $body['seed']);
    }

    public function testGenerateRejectsAnEmptyModel(): void
    {
        $this->expectException(\OpenRouter\Exceptions\InvalidArgumentException::class);

        new CreateVideoRequest(model: '');
    }

    public function testGenerateReturnsATypedJob(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(VideosJobFixture::ATTRIBUTES);

        $response = $this->client($http)->videos()->generate(['model' => 'google/veo-3']);

        $this->assertInstanceOf(VideoJobResponse::class, $response);
        $this->assertSame('vid_job_01HQ8Z3K4M5N6P7Q8R9S', $response->id);
        $this->assertSame('pending', $response->status);
        $this->assertSame('gen_01HQ8Z3K4M5N6P7Q8R9S', $response->generationId);
        $this->assertSame(['https://cdn.openrouter.ai/videos/out-0.mp4'], $response->unsignedUrls);
        $this->assertSame(0.42, $response->usage?->cost);
        $this->assertFalse($response->usage?->isByok);
        $this->assertNull($response->error);
    }

    public function testRetrievePollsTheJob(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(VideosJobFixture::ATTRIBUTES);

        $response = $this->client($http)->videos()->retrieve('vid_job_1');

        $this->assertSame('GET', $http->lastRequest()->getMethod());
        $this->assertStringEndsWith('/videos/vid_job_1', (string) $http->lastRequest()->getUri());
        $this->assertSame('pending', $response->status);
    }

    public function testDownloadReturnsRawBytesAndPassesTheIndex(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueBinary('mp4-bytes', 'video/mp4');

        $response = $this->client($http)->videos()->download('vid_job_1', index: 2);

        $uri = (string) $http->lastRequest()->getUri();
        $this->assertStringContainsString('/videos/vid_job_1/content', $uri);
        $this->assertStringContainsString('index=2', $uri);
        $this->assertInstanceOf(BinaryResponse::class, $response);
        $this->assertSame('video/mp4', $response->contentType);
    }

    public function testListModelsReturnsTypedModels(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(VideosModelsFixture::ATTRIBUTES);

        $response = $this->client($http)->videos()->listModels();

        $this->assertStringEndsWith('/videos/models', (string) $http->lastRequest()->getUri());
        $this->assertInstanceOf(ListVideoModelsResponse::class, $response);
        $this->assertCount(1, $response->data);
        $this->assertSame('google/veo-3', $response->data[0]->id);
        $this->assertSame('Veo 3', $response->data[0]->name);
        $this->assertSame([4, 8], $response->data[0]->supportedDurations);
        $this->assertTrue($response->data[0]->generateAudio);
    }

    public function testJobKeepsUnknownFieldsInExtras(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson([...VideosJobFixture::ATTRIBUTES, 'a_new_field' => 'x']);

        $response = $this->client($http)->videos()->retrieve('vid_job_1');

        $this->assertSame('x', $response->extras['a_new_field']);
    }
}
