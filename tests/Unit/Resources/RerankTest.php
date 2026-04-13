<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Resources;

use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\Factory;
use OpenRouter\Responses\Rerank\RerankResponse;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use OpenRouter\Tests\Fixtures\RerankFixture;
use OpenRouter\ValueObjects\Rerank\RerankRequest;
use PHPUnit\Framework\TestCase;

final class RerankTest extends TestCase
{
    public function testRerankHitsRerankEndpointAsPost(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(RerankFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $client->rerank()->rerank([
            'model' => 'cohere/rerank-v3.5',
            'query' => 'What is the capital of France?',
            'documents' => ['Paris is the capital of France.', 'Berlin is the capital of Germany.'],
        ]);

        $request = $http->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/rerank', (string) $request->getUri());

        $body = json_decode((string) $request->getBody(), true);
        $this->assertSame('cohere/rerank-v3.5', $body['model']);
        $this->assertSame('What is the capital of France?', $body['query']);
        $this->assertCount(2, $body['documents']);
    }

    public function testRerankSerializesTypedRequest(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(RerankFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $request = new RerankRequest(
            model: 'cohere/rerank-v3.5',
            query: 'What is the capital of France?',
            documents: ['Paris is the capital of France.', 'Berlin is the capital of Germany.'],
            topN: 3,
        );

        $client->rerank()->rerank($request);

        $body = json_decode((string) $http->lastRequest()->getBody(), true);
        $this->assertSame('cohere/rerank-v3.5', $body['model']);
        $this->assertSame(3, $body['top_n']);
    }

    public function testRerankReturnsTypedResponse(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(RerankFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $response = $client->rerank()->rerank([
            'model' => 'cohere/rerank-v3.5',
            'query' => 'What is the capital of France?',
            'documents' => ['Paris is the capital of France.'],
        ]);

        $this->assertInstanceOf(RerankResponse::class, $response);
        $this->assertSame('gen-rerank-1234567890-abc', $response->id);
        $this->assertSame('cohere/rerank-v3.5', $response->model);
        $this->assertSame('Cohere', $response->provider);
        $this->assertCount(2, $response->results);

        $first = $response->results[0];
        $this->assertSame(0, $first->index);
        $this->assertSame(0.98, $first->relevanceScore);
        $this->assertSame('Paris is the capital of France.', $first->document->text);

        $this->assertNotNull($response->usage);
        $this->assertSame(1, $response->usage->searchUnits);
        $this->assertSame(150, $response->usage->totalTokens);
        $this->assertSame(0.001, $response->usage->cost);
    }

    public function testRerankRequestRejectsEmptyDocuments(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new RerankRequest(model: 'cohere/rerank-v3.5', query: 'q', documents: []);
    }

    public function testRerankRequestRejectsEmptyModel(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new RerankRequest(model: '', query: 'q', documents: ['d']);
    }
}
