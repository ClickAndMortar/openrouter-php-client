<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Resources;

use OpenRouter\Enums\Embeddings\EncodingFormat;
use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\Factory;
use OpenRouter\Responses\Embeddings\CreateEmbeddingsResponse;
use OpenRouter\Responses\Models\ListResponse;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use OpenRouter\Tests\Fixtures\EmbeddingsCreateFixture;
use OpenRouter\Tests\Fixtures\EmbeddingsModelsListFixture;
use OpenRouter\ValueObjects\Embeddings\CreateEmbeddingsRequest;
use PHPUnit\Framework\TestCase;

final class EmbeddingsTest extends TestCase
{
    public function testGenerateHitsEmbeddingsEndpointAsPost(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(EmbeddingsCreateFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $client->embeddings()->generate([
            'model' => 'openai/text-embedding-3-small',
            'input' => 'The quick brown fox jumps over the lazy dog',
        ]);

        $request = $http->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/embeddings', (string) $request->getUri());

        $body = json_decode((string) $request->getBody(), true);
        $this->assertSame('openai/text-embedding-3-small', $body['model']);
        $this->assertSame('The quick brown fox jumps over the lazy dog', $body['input']);
    }

    public function testGenerateSerializesTypedRequest(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(EmbeddingsCreateFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $request = new CreateEmbeddingsRequest(
            input: ['hello', 'world'],
            model: 'openai/text-embedding-3-small',
            dimensions: 1536,
            encodingFormat: EncodingFormat::Float,
            inputType: 'search_query',
            user: 'user-1234',
        );

        $client->embeddings()->generate($request);

        $body = json_decode((string) $http->lastRequest()->getBody(), true);
        $this->assertSame(['hello', 'world'], $body['input']);
        $this->assertSame('openai/text-embedding-3-small', $body['model']);
        $this->assertSame(1536, $body['dimensions']);
        $this->assertSame('float', $body['encoding_format']);
        $this->assertSame('search_query', $body['input_type']);
        $this->assertSame('user-1234', $body['user']);
    }

    public function testGenerateReturnsTypedResponse(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(EmbeddingsCreateFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $response = $client->embeddings()->generate([
            'model' => 'openai/text-embedding-3-small',
            'input' => 'hello',
        ]);

        $this->assertInstanceOf(CreateEmbeddingsResponse::class, $response);
        $this->assertSame('embd-1234567890', $response->id);
        $this->assertSame('list', $response->object);
        $this->assertSame('openai/text-embedding-3-small', $response->model);

        $this->assertCount(1, $response->data);
        $entry = $response->data[0];
        $this->assertSame('embedding', $entry->object);
        $this->assertSame(0, $entry->index);
        $this->assertSame([0.0023064255, -0.009327292, 0.015797347], $entry->embedding);

        $this->assertNotNull($response->usage);
        $this->assertSame(8, $response->usage->promptTokens);
        $this->assertSame(8, $response->usage->totalTokens);
        $this->assertSame(0.0001, $response->usage->cost);
    }

    public function testCreateEmbeddingsRequestRejectsEmptyModel(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CreateEmbeddingsRequest(input: 'hello', model: '');
    }

    public function testCreateEmbeddingsRequestRejectsEmptyInput(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CreateEmbeddingsRequest(input: '', model: 'openai/text-embedding-3-small');
    }

    public function testListModelsHitsEmbeddingsModelsEndpointAsGet(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(EmbeddingsModelsListFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $client->embeddings()->listModels();

        $request = $http->lastRequest();
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/embeddings/models', (string) $request->getUri());
        $this->assertSame('', (string) $request->getBody());
    }

    public function testListModelsReturnsTypedListResponse(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(EmbeddingsModelsListFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $response = $client->embeddings()->listModels();

        $this->assertInstanceOf(ListResponse::class, $response);
        $this->assertCount(1, $response->data);

        $model = $response->data[0];
        $this->assertSame('openai/text-embedding-3-small', $model->id);
        $this->assertSame('Text Embedding 3 Small', $model->name);
        $this->assertSame(['embeddings'], $model->architecture->outputModalities);
        $this->assertSame('0.00000002', $model->pricing->prompt);
        $this->assertFalse($model->topProvider->isModerated);
    }
}
