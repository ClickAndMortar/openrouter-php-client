<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Resources;

use OpenRouter\Factory;
use OpenRouter\Responses\Models\CountResponse;
use OpenRouter\Responses\Models\ListEndpointsResponse;
use OpenRouter\Responses\Models\ListResponse;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use OpenRouter\Tests\Fixtures\ModelsCountFixture;
use OpenRouter\Tests\Fixtures\ModelsListEndpointsFixture;
use OpenRouter\Tests\Fixtures\ModelsListFixture;
use OpenRouter\Tests\Fixtures\ModelsListForUserFixture;
use PHPUnit\Framework\TestCase;

final class ModelsTest extends TestCase
{
    public function testListForUserHitsCorrectEndpointAsGet(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ModelsListForUserFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $client->models()->listForUser();

        $request = $http->lastRequest();
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/models/user', (string) $request->getUri());
        $this->assertSame('', (string) $request->getBody());
    }

    public function testListForUserReturnsTypedListResponse(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ModelsListForUserFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $response = $client->models()->listForUser();

        $this->assertInstanceOf(ListResponse::class, $response);
        $this->assertCount(1, $response->data);

        $model = $response->data[0];
        $this->assertSame('openai/gpt-4', $model->id);
        $this->assertSame('openai/gpt-4', $model->canonicalSlug);
        $this->assertSame('GPT-4', $model->name);
        $this->assertSame(1692901234, $model->created);
        $this->assertSame(8192, $model->contextLength);

        $this->assertSame(['text'], $model->architecture->inputModalities);
        $this->assertSame(['text'], $model->architecture->outputModalities);
        $this->assertSame('chatml', $model->architecture->instructType);
        $this->assertSame('text->text', $model->architecture->modality);
        $this->assertSame('GPT', $model->architecture->tokenizer);

        $this->assertSame('0.00003', $model->pricing->prompt);
        $this->assertSame('0.00006', $model->pricing->completion);
        $this->assertSame('0', $model->pricing->image);

        $this->assertTrue($model->topProvider->isModerated);
        $this->assertSame(8192, $model->topProvider->contextLength);
        $this->assertSame(4096, $model->topProvider->maxCompletionTokens);

        $this->assertSame(['temperature', 'top_p', 'max_tokens'], $model->supportedParameters);
    }

    public function testListHitsCorrectEndpointAsGet(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ModelsListFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $client->models()->list();

        $request = $http->lastRequest();
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/models', (string) $request->getUri());
        $this->assertSame('', (string) $request->getBody());
    }

    public function testListForwardsQueryFilters(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ModelsListFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $client->models()->list(
            category: 'programming',
            supportedParameters: 'temperature',
            outputModalities: 'text',
        );

        $uri = (string) $http->lastRequest()->getUri();
        $this->assertStringContainsString('category=programming', $uri);
        $this->assertStringContainsString('supported_parameters=temperature', $uri);
        $this->assertStringContainsString('output_modalities=text', $uri);
    }

    public function testListReturnsTypedListResponse(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ModelsListFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $response = $client->models()->list();

        $this->assertInstanceOf(ListResponse::class, $response);
        $this->assertCount(1, $response->data);
        $this->assertSame('openai/gpt-4', $response->data[0]->id);
    }

    public function testCountHitsCorrectEndpoint(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ModelsCountFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $client->models()->count();

        $request = $http->lastRequest();
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/models/count', (string) $request->getUri());
    }

    public function testCountForwardsOutputModalities(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ModelsCountFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $client->models()->count(outputModalities: 'text,image');

        $uri = (string) $http->lastRequest()->getUri();
        $this->assertStringContainsString('output_modalities=text%2Cimage', $uri);
    }

    public function testCountReturnsTypedResponse(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ModelsCountFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $response = $client->models()->count();

        $this->assertInstanceOf(CountResponse::class, $response);
        $this->assertSame(150, $response->count);
        $this->assertSame(['data' => ['count' => 150]], $response->toArray());
    }

    public function testListEndpointsUsesAuthorSlugPath(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ModelsListEndpointsFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $client->models()->listEndpoints('openai', 'gpt-4');

        $request = $http->lastRequest();
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/models/openai/gpt-4/endpoints', (string) $request->getUri());
    }

    public function testListEndpointsReturnsTypedResponse(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ModelsListEndpointsFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $response = $client->models()->listEndpoints('openai', 'gpt-4');

        $this->assertInstanceOf(ListEndpointsResponse::class, $response);
        $this->assertSame('openai/gpt-4', $response->data->id);
        $this->assertSame('GPT-4', $response->data->name);
        $this->assertSame(1692901234, $response->data->created);
        $this->assertSame(['text'], $response->data->architecture->inputModalities);

        $this->assertCount(1, $response->data->endpoints);
        $endpoint = $response->data->endpoints[0];

        $this->assertSame('OpenAI: GPT-4', $endpoint->name);
        $this->assertSame('openai/gpt-4', $endpoint->modelId);
        $this->assertSame('GPT-4', $endpoint->modelName);
        $this->assertSame(8192, $endpoint->contextLength);
        $this->assertSame(4096, $endpoint->maxCompletionTokens);
        $this->assertSame(8192, $endpoint->maxPromptTokens);
        $this->assertSame('OpenAI', $endpoint->providerName);
        $this->assertSame('fp16', $endpoint->quantization);
        $this->assertSame('default', $endpoint->status);
        $this->assertSame('openai', $endpoint->tag);
        $this->assertTrue($endpoint->supportsImplicitCaching);
        $this->assertSame(['temperature', 'top_p', 'max_tokens'], $endpoint->supportedParameters);

        $this->assertSame('0.00003', $endpoint->pricing->prompt);
        $this->assertSame('0.00006', $endpoint->pricing->completion);

        $this->assertNotNull($endpoint->latencyLast30m);
        $this->assertSame(0.25, $endpoint->latencyLast30m->p50);
        $this->assertSame(0.85, $endpoint->latencyLast30m->p99);

        $this->assertNotNull($endpoint->throughputLast30m);
        $this->assertSame(45.2, $endpoint->throughputLast30m->p50);

        $this->assertSame(99.8, $endpoint->uptimeLast1d);
        $this->assertSame(99.5, $endpoint->uptimeLast30m);
        $this->assertSame(100, $endpoint->uptimeLast5m);
    }
}
