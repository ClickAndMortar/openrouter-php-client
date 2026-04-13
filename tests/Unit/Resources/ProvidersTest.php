<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Resources;

use OpenRouter\Factory;
use OpenRouter\Responses\Providers\ListProvidersResponse;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use OpenRouter\Tests\Fixtures\ProvidersListFixture;
use PHPUnit\Framework\TestCase;

final class ProvidersTest extends TestCase
{
    public function testListHitsProvidersEndpointAsGet(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ProvidersListFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $client->providers()->list();

        $request = $http->lastRequest();
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/providers', (string) $request->getUri());
        $this->assertSame('', (string) $request->getBody());
    }

    public function testListReturnsTypedResponse(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ProvidersListFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $response = $client->providers()->list();

        $this->assertInstanceOf(ListProvidersResponse::class, $response);
        $this->assertCount(1, $response->data);

        $provider = $response->data[0];
        $this->assertSame('openai', $provider->slug);
        $this->assertSame('OpenAI', $provider->name);
        $this->assertSame('US', $provider->headquarters);
        $this->assertSame(['US', 'IE'], $provider->datacenters);
        $this->assertSame('https://openai.com/privacy', $provider->privacyPolicyUrl);
        $this->assertSame('https://openai.com/terms', $provider->termsOfServiceUrl);
        $this->assertSame('https://status.openai.com', $provider->statusPageUrl);
    }
}
