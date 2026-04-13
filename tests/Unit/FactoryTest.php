<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit;

use OpenRouter\Client;
use OpenRouter\Factory;
use OpenRouter\OpenRouter;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use OpenRouter\Tests\Doubles\SpyLogger;
use OpenRouter\Tests\Fixtures\ResponsesCreateFixture;
use OpenRouter\Transporters\RetryConfig;
use PHPUnit\Framework\TestCase;

final class FactoryTest extends TestCase
{
    public function testOpenRouterClientStaticEntryReturnsClient(): void
    {
        $factory = new Factory();
        $factory->withHttpClient(new RecordingHttpClient());

        $client = $factory->withApiKey('sk-or-test')->make();

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testOpenRouterClientShortcutReturnsClient(): void
    {
        $client = OpenRouter::client('sk-or-test');

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testRequestUsesDefaultOpenRouterBaseUri(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ResponsesCreateFixture::ATTRIBUTES);

        $client = (new Factory())
            ->withApiKey('sk-or-test')
            ->withHttpClient($http)
            ->make();

        $client->responses()->send(['model' => 'openai/gpt-4o', 'input' => 'hi']);

        $uri = (string) $http->lastRequest()->getUri();
        $this->assertStringStartsWith('https://openrouter.ai/api/v1/', $uri);
    }

    public function testFactoryAppliesCustomBaseUri(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ResponsesCreateFixture::ATTRIBUTES);

        $client = (new Factory())
            ->withApiKey('sk-or-test')
            ->withBaseUri('https://eu.openrouter.ai/api/v1')
            ->withHttpClient($http)
            ->make();

        $client->responses()->send(['model' => 'openai/gpt-4o', 'input' => 'hi']);

        $this->assertStringStartsWith(
            'https://eu.openrouter.ai/api/v1/',
            (string) $http->lastRequest()->getUri(),
        );
    }

    public function testFactoryWiresBearerAuthorizationHeader(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ResponsesCreateFixture::ATTRIBUTES);

        $client = (new Factory())
            ->withApiKey('sk-or-secret')
            ->withHttpClient($http)
            ->make();

        $client->responses()->send(['model' => 'openai/gpt-4o', 'input' => 'hi']);

        $this->assertSame('Bearer sk-or-secret', $http->lastRequest()->getHeaderLine('Authorization'));
    }

    public function testFactoryWiresHttpRefererAppTitleAndAppCategories(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ResponsesCreateFixture::ATTRIBUTES);

        $client = (new Factory())
            ->withApiKey('sk-or-test')
            ->withHttpReferer('https://myapp.example.com')
            ->withAppTitle('My App')
            ->withAppCategories(['cli-agent', 'cloud-agent'])
            ->withHttpClient($http)
            ->make();

        $client->responses()->send(['model' => 'openai/gpt-4o', 'input' => 'hi']);

        $request = $http->lastRequest();
        $this->assertSame('https://myapp.example.com', $request->getHeaderLine('HTTP-Referer'));
        $this->assertSame('My App', $request->getHeaderLine('X-Title'));
        $this->assertSame('cli-agent,cloud-agent', $request->getHeaderLine('X-OpenRouter-Categories'));
    }

    public function testFactoryAcceptsAppCategoriesAsString(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ResponsesCreateFixture::ATTRIBUTES);

        $client = (new Factory())
            ->withApiKey('sk-or-test')
            ->withAppCategories('cli-agent,cloud-agent')
            ->withHttpClient($http)
            ->make();

        $client->responses()->send(['model' => 'openai/gpt-4o', 'input' => 'hi']);

        $this->assertSame(
            'cli-agent,cloud-agent',
            $http->lastRequest()->getHeaderLine('X-OpenRouter-Categories'),
        );
    }

    public function testFactoryAppliesSessionIdHeader(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ResponsesCreateFixture::ATTRIBUTES);

        $client = (new Factory())
            ->withApiKey('sk-or-test')
            ->withSessionId('sess-abc')
            ->withHttpClient($http)
            ->make();

        $client->responses()->send(['model' => 'openai/gpt-4o', 'input' => 'hi']);

        $this->assertSame('sess-abc', $http->lastRequest()->getHeaderLine('x-session-id'));
    }

    public function testFactoryWiresLoggerThroughToTransporter(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['error' => ['message' => 'boom']], 503);
        $http->enqueueJson(ResponsesCreateFixture::ATTRIBUTES, 200);
        $logger = new SpyLogger();

        $client = (new Factory())
            ->withApiKey('sk-test')
            ->withHttpClient($http)
            ->withRetry(new RetryConfig(maxAttempts: 2, initialDelayMs: 1, sleeper: static fn () => null))
            ->withLogger($logger)
            ->make();

        $client->responses()->send(['model' => 'm', 'input' => 'i']);

        $this->assertCount(1, $logger->byLevel('warning'));
    }

    public function testFactoryAppliesCustomHeader(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ResponsesCreateFixture::ATTRIBUTES);

        $client = (new Factory())
            ->withApiKey('sk-or-test')
            ->withHttpHeader('X-Trace-Id', 'trace-123')
            ->withHttpClient($http)
            ->make();

        $client->responses()->send(['model' => 'openai/gpt-4o', 'input' => 'hi']);

        $this->assertSame('trace-123', $http->lastRequest()->getHeaderLine('X-Trace-Id'));
    }
}
