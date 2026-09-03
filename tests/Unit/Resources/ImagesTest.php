<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Resources;

use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\Factory;
use OpenRouter\Responses\Images\ImagesResult;
use OpenRouter\Responses\Images\ListImageModelEndpointsResponse;
use OpenRouter\Responses\Images\ListImageModelsResponse;
use OpenRouter\Responses\Images\Stream\ImageStreamCompletedEvent;
use OpenRouter\Responses\Images\Stream\ImageStreamEvent;
use OpenRouter\Responses\Images\Stream\ImageStreamPartialImageEvent;
use OpenRouter\Responses\Images\Stream\ImageStreamTextChunkEvent;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use OpenRouter\Tests\Fixtures\ImagesGenerateFixture;
use OpenRouter\Tests\Fixtures\ImagesModelEndpointsFixture;
use OpenRouter\Tests\Fixtures\ImagesModelsFixture;
use OpenRouter\ValueObjects\Images\CreateImageRequest;
use PHPUnit\Framework\TestCase;

final class ImagesTest extends TestCase
{
    private function client(RecordingHttpClient $http): \OpenRouter\Client
    {
        return (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();
    }

    public function testGeneratePostsTheRequestBody(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ImagesGenerateFixture::ATTRIBUTES);

        $this->client($http)->images()->generate(new CreateImageRequest(
            model: 'openai/gpt-image-1',
            prompt: 'A cat surfing',
            n: 2,
            size: '1024x1024',
            outputFormat: 'png',
        ));

        $request = $http->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/images', (string) $request->getUri());

        $body = json_decode((string) $request->getBody(), true);
        $this->assertSame('openai/gpt-image-1', $body['model']);
        $this->assertSame('A cat surfing', $body['prompt']);
        $this->assertSame(2, $body['n']);
        $this->assertSame('1024x1024', $body['size']);
        $this->assertSame('png', $body['output_format']);
    }

    public function testGenerateRejectsAnEmptyPrompt(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CreateImageRequest(model: 'openai/gpt-image-1', prompt: '');
    }

    public function testGenerateReturnsTypedImages(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ImagesGenerateFixture::ATTRIBUTES);

        $result = $this->client($http)->images()->generate(['model' => 'm', 'prompt' => 'p']);

        $this->assertInstanceOf(ImagesResult::class, $result);
        $this->assertSame(1747848842, $result->created);
        $this->assertCount(1, $result->data);
        $this->assertSame('iVBORw0KGgoAAAANSUhEUg==', $result->data[0]->b64Json);
        $this->assertSame('image/png', $result->data[0]->mediaType);
        $this->assertSame(12, $result->usage?->totalTokens);
        $this->assertSame(0.004, $result->usage?->cost);
    }

    public function testGenerateExposesDecodedBinaryForTheFirstImage(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ImagesGenerateFixture::ATTRIBUTES);

        $result = $this->client($http)->images()->generate(['model' => 'm', 'prompt' => 'p']);

        $this->assertSame(base64_decode('iVBORw0KGgoAAAANSUhEUg==', true), $result->data[0]->binary());
    }

    public function testGenerateRejectsStreamTrue(): void
    {
        $http = new RecordingHttpClient();

        $this->expectException(InvalidArgumentException::class);

        $this->client($http)->images()->generate(['model' => 'm', 'prompt' => 'p', 'stream' => true]);
    }

    public function testGenerateStreamedYieldsTypedEvents(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueStream(implode('', [
            "data: {\"type\":\"image_generation.text_chunk\",\"text\":\"drawing\",\"phase\":\"reasoning\"}\n\n",
            "data: {\"type\":\"image_generation.partial_image\",\"b64_json\":\"AAA=\",\"partial_image_index\":0}\n\n",
            "data: {\"type\":\"image_generation.completed\",\"b64_json\":\"BBB=\",\"created\":1747848842,\"media_type\":\"image/png\"}\n\n",
            "data: [DONE]\n\n",
        ]));

        $events = iterator_to_array($this->client($http)->images()->generateStreamed([
            'model' => 'openai/gpt-image-1',
            'prompt' => 'A cat',
        ]));

        $body = json_decode((string) $http->lastRequest()->getBody(), true);
        $this->assertTrue($body['stream'], 'generateStreamed must set stream=true');

        $this->assertCount(3, $events);

        $this->assertInstanceOf(ImageStreamTextChunkEvent::class, $events[0]);
        $this->assertSame('drawing', $events[0]->text);
        $this->assertSame('reasoning', $events[0]->phase);

        $this->assertInstanceOf(ImageStreamPartialImageEvent::class, $events[1]);
        $this->assertSame('AAA=', $events[1]->b64Json);
        $this->assertSame(0, $events[1]->partialImageIndex);

        $this->assertInstanceOf(ImageStreamCompletedEvent::class, $events[2]);
        $this->assertSame('BBB=', $events[2]->b64Json);
        $this->assertSame('image/png', $events[2]->mediaType);
        $this->assertSame(1747848842, $events[2]->created);
    }

    public function testUnknownStreamEventFallsBackToTheBaseClass(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueStream("data: {\"type\":\"image_generation.brand_new\",\"foo\":1}\n\n" ."data: [DONE]\n\n");

        $events = iterator_to_array($this->client($http)->images()->generateStreamed(['model' => 'm', 'prompt' => 'p']));

        $this->assertCount(1, $events);
        $this->assertInstanceOf(ImageStreamEvent::class, $events[0]);
        $this->assertSame('image_generation.brand_new', $events[0]->type);
        $this->assertSame(1, $events[0]->attributes['foo']);
    }

    public function testListModelsReturnsTypedModels(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ImagesModelsFixture::ATTRIBUTES);

        $response = $this->client($http)->images()->listModels();

        $this->assertStringEndsWith('/images/models', (string) $http->lastRequest()->getUri());
        $this->assertInstanceOf(ListImageModelsResponse::class, $response);
        $this->assertSame('openai/gpt-image-1', $response->data[0]->id);
        $this->assertTrue($response->data[0]->supportsStreaming);
        $this->assertSame(['size', 'quality'], $response->data[0]->supportedParameters);
    }

    public function testListEndpointsReturnsTypedEndpoints(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ImagesModelEndpointsFixture::ATTRIBUTES);

        $response = $this->client($http)->images()->listEndpoints('openai', 'gpt-image-1');

        $this->assertStringEndsWith(
            '/images/models/openai/gpt-image-1/endpoints',
            (string) $http->lastRequest()->getUri(),
        );
        $this->assertInstanceOf(ListImageModelEndpointsResponse::class, $response);
        $this->assertSame('openai/gpt-image-1', $response->id);
        $this->assertCount(1, $response->endpoints);
        $this->assertSame('OpenAI', $response->endpoints[0]->providerName);
        $this->assertTrue($response->endpoints[0]->supportsStreaming);
    }
}
