<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Resources;

use OpenRouter\Factory;
use OpenRouter\Responses\Generation\RetrieveGenerationResponse;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use OpenRouter\Tests\Fixtures\GenerationRetrieveFixture;
use PHPUnit\Framework\TestCase;

final class GenerationTest extends TestCase
{
    public function testRetrieveHitsGenerationEndpointAsGet(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(GenerationRetrieveFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $client->generation()->retrieve('gen-3bhGkxlo4XFrqiabUM7NDtwDzWwG');

        $request = $http->lastRequest();
        $this->assertSame('GET', $request->getMethod());

        $uri = (string) $request->getUri();
        $this->assertStringContainsString('/generation', $uri);
        $this->assertStringContainsString('id=gen-3bhGkxlo4XFrqiabUM7NDtwDzWwG', $uri);
        $this->assertSame('', (string) $request->getBody());
    }

    public function testRetrieveReturnsTypedResponse(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(GenerationRetrieveFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $response = $client->generation()->retrieve('gen-3bhGkxlo4XFrqiabUM7NDtwDzWwG');

        $this->assertInstanceOf(RetrieveGenerationResponse::class, $response);

        $data = $response->data;
        $this->assertSame('gen-3bhGkxlo4XFrqiabUM7NDtwDzWwG', $data->id);
        $this->assertSame('chatcmpl-791bcf62-080e-4568-87d0-94c72e3b4946', $data->upstreamId);
        $this->assertSame('sao10k/l3-stheno-8b', $data->model);
        $this->assertSame(12345, $data->appId);
        $this->assertSame('Infermatic', $data->providerName);
        $this->assertSame(0.0015, $data->totalCost);
        $this->assertSame(0.0002, $data->cacheDiscount);
        $this->assertSame(0.0012, $data->upstreamInferenceCost);
        $this->assertSame('stop', $data->finishReason);
        $this->assertSame('stop', $data->nativeFinishReason);
        $this->assertSame(10, $data->tokensPrompt);
        $this->assertSame(25, $data->tokensCompletion);
        $this->assertSame(10, $data->nativeTokensPrompt);
        $this->assertSame(25, $data->nativeTokensCompletion);
        $this->assertSame(5, $data->nativeTokensReasoning);
        $this->assertSame(3, $data->nativeTokensCached);
        $this->assertSame(1, $data->numMediaPrompt);
        $this->assertSame(5, $data->numSearchResults);
        $this->assertSame(1250.0, $data->latency);
        $this->assertSame(50.0, $data->moderationLatency);
        $this->assertSame(1200.0, $data->generationTime);
        $this->assertTrue($data->streamed);
        $this->assertFalse($data->cancelled);
        $this->assertFalse($data->isByok);
        $this->assertSame('user-123', $data->externalUser);
        $this->assertNull($data->apiType);
        $this->assertNull($data->providerResponses);
        $this->assertSame('req-1727282430-aBcDeFgHiJkLmNoPqRsT', $data->requestId);
    }
}
