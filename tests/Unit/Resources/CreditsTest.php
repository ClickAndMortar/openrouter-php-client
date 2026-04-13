<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Resources;

use OpenRouter\Exceptions\ErrorException;
use OpenRouter\Factory;
use OpenRouter\Responses\Credits\RetrieveCreditsResponse;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use OpenRouter\Tests\Fixtures\CreditsRetrieveFixture;
use PHPUnit\Framework\TestCase;

final class CreditsTest extends TestCase
{
    public function testRetrieveHitsCreditsEndpointAsGet(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(CreditsRetrieveFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $client->credits()->retrieve();

        $request = $http->lastRequest();
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/credits', (string) $request->getUri());
        $this->assertSame('', (string) $request->getBody());
    }

    public function testRetrieveReturnsTypedResponse(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(CreditsRetrieveFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $response = $client->credits()->retrieve();

        $this->assertInstanceOf(RetrieveCreditsResponse::class, $response);
        $this->assertSame(100.5, $response->data->totalCredits);
        $this->assertSame(25.75, $response->data->totalUsage);
        $this->assertSame(
            ['data' => ['total_credits' => 100.5, 'total_usage' => 25.75]],
            $response->toArray(),
        );
    }

    public function testCreateCoinbaseChargeRaisesGoneError(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(
            [
                'error' => [
                    'code' => 410,
                    'message' => 'The Coinbase APIs used by this endpoint have been deprecated, so the Coinbase Commerce credits API has been removed. Use the web credits purchase flow instead.',
                ],
            ],
            statusCode: 410,
        );

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $this->expectException(ErrorException::class);

        try {
            $client->credits()->createCoinbaseCharge();
        } finally {
            $request = $http->lastRequest();
            $this->assertSame('POST', $request->getMethod());
            $this->assertStringEndsWith('/credits/coinbase', (string) $request->getUri());
        }
    }
}
