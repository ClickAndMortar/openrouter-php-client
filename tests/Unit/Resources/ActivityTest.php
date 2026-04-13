<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Resources;

use OpenRouter\Factory;
use OpenRouter\Responses\Activity\ListActivityResponse;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use OpenRouter\Tests\Fixtures\ActivityListFixture;
use PHPUnit\Framework\TestCase;

final class ActivityTest extends TestCase
{
    public function testListHitsActivityEndpointAsGet(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ActivityListFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $client->activity()->list();

        $request = $http->lastRequest();
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/activity', (string) $request->getUri());
        $this->assertSame('', (string) $request->getBody());
    }

    public function testListForwardsQueryFilters(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ActivityListFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $client->activity()->list(
            date: '2025-08-24',
            apiKeyHash: 'abc123def456',
            userId: 'user_abc123',
        );

        $uri = (string) $http->lastRequest()->getUri();
        $this->assertStringContainsString('date=2025-08-24', $uri);
        $this->assertStringContainsString('api_key_hash=abc123def456', $uri);
        $this->assertStringContainsString('user_id=user_abc123', $uri);
    }

    public function testListReturnsTypedResponse(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ActivityListFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $response = $client->activity()->list();

        $this->assertInstanceOf(ListActivityResponse::class, $response);
        $this->assertCount(1, $response->data);

        $item = $response->data[0];
        $this->assertSame('2025-08-24', $item->date);
        $this->assertSame('openai/gpt-4.1', $item->model);
        $this->assertSame('openai/gpt-4.1-2025-04-14', $item->modelPermaslug);
        $this->assertSame('550e8400-e29b-41d4-a716-446655440000', $item->endpointId);
        $this->assertSame('OpenAI', $item->providerName);
        $this->assertSame(0.015, $item->usage);
        $this->assertSame(0.012, $item->byokUsageInference);
        $this->assertSame(5, $item->requests);
        $this->assertSame(50, $item->promptTokens);
        $this->assertSame(125, $item->completionTokens);
        $this->assertSame(25, $item->reasoningTokens);
    }
}
