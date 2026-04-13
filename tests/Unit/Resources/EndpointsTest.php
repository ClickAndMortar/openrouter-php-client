<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Resources;

use OpenRouter\Factory;
use OpenRouter\Responses\Endpoints\ListZdrEndpointsResponse;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use OpenRouter\Tests\Fixtures\EndpointsListZdrFixture;
use PHPUnit\Framework\TestCase;

final class EndpointsTest extends TestCase
{
    public function testListZdrHitsEndpointsZdrEndpointAsGet(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(EndpointsListZdrFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $client->endpoints()->listZdr();

        $request = $http->lastRequest();
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/endpoints/zdr', (string) $request->getUri());
        $this->assertSame('', (string) $request->getBody());
    }

    public function testListZdrReturnsTypedResponse(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(EndpointsListZdrFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $response = $client->endpoints()->listZdr();

        $this->assertInstanceOf(ListZdrEndpointsResponse::class, $response);
        $this->assertCount(1, $response->data);

        $endpoint = $response->data[0];
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
