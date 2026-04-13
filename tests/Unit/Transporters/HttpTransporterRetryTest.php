<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Transporters;

use OpenRouter\Exceptions\Http\InternalServerErrorException;
use OpenRouter\Exceptions\Http\UnauthorizedException;
use OpenRouter\Factory;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use OpenRouter\Tests\Fixtures\ResponsesCreateFixture;
use OpenRouter\Transporters\RetryConfig;
use PHPUnit\Framework\TestCase;

final class HttpTransporterRetryTest extends TestCase
{
    public function testRetryDisabledByDefaultDoesNotRetry500(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['error' => ['message' => 'boom']], 500);

        $client = (new Factory())
            ->withApiKey('sk-test')
            ->withHttpClient($http)
            ->make();

        $this->expectException(InternalServerErrorException::class);

        try {
            $client->responses()->send(['model' => 'm', 'input' => 'i']);
        } finally {
            $this->assertCount(1, $http->requests);
        }
    }

    public function testRetryOn500ThenSuccess(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['error' => ['message' => 'boom']], 500);
        $http->enqueueJson(['error' => ['message' => 'boom']], 503);
        $http->enqueueJson(ResponsesCreateFixture::ATTRIBUTES, 200);

        $slept = [];
        $config = new RetryConfig(
            maxAttempts: 3,
            initialDelayMs: 100,
            maxDelayMs: 10_000,
            multiplier: 2.0,
            sleeper: function (int $ms) use (&$slept): void {
                $slept[] = $ms;
            },
        );

        $client = (new Factory())
            ->withApiKey('sk-test')
            ->withHttpClient($http)
            ->withRetry($config)
            ->make();

        $result = $client->responses()->send(['model' => 'm', 'input' => 'i']);

        $this->assertCount(3, $http->requests);
        $this->assertSame([100, 200], $slept);
        $this->assertNotEmpty($result->id);
    }

    public function testRetryHonoursMaxAttempts(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['error' => ['message' => 'boom']], 500);
        $http->enqueueJson(['error' => ['message' => 'boom']], 500);

        $config = new RetryConfig(
            maxAttempts: 2,
            initialDelayMs: 1,
            sleeper: static fn (int $ms) => null,
        );

        $client = (new Factory())
            ->withApiKey('sk-test')
            ->withHttpClient($http)
            ->withRetry($config)
            ->make();

        $this->expectException(InternalServerErrorException::class);

        try {
            $client->responses()->send(['model' => 'm', 'input' => 'i']);
        } finally {
            $this->assertCount(2, $http->requests);
        }
    }

    public function testRetryDoesNotRetry4xx(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['error' => ['message' => 'unauth']], 401);

        $config = new RetryConfig(
            maxAttempts: 3,
            initialDelayMs: 1,
            sleeper: static fn (int $ms) => null,
        );

        $client = (new Factory())
            ->withApiKey('sk-test')
            ->withHttpClient($http)
            ->withRetry($config)
            ->make();

        $this->expectException(UnauthorizedException::class);

        try {
            $client->responses()->send(['model' => 'm', 'input' => 'i']);
        } finally {
            $this->assertCount(1, $http->requests);
        }
    }

    public function testRetryConfigDelayCappedAtMax(): void
    {
        $config = new RetryConfig(
            maxAttempts: 10,
            initialDelayMs: 500,
            maxDelayMs: 1_000,
            multiplier: 2.0,
        );

        $this->assertSame(0, $config->delayForAttempt(1));
        $this->assertSame(500, $config->delayForAttempt(2));
        $this->assertSame(1_000, $config->delayForAttempt(3));
        $this->assertSame(1_000, $config->delayForAttempt(10));
    }

    public function testRetryConfigRejectsInvalidAttempts(): void
    {
        $this->expectException(\OpenRouter\Exceptions\InvalidArgumentException::class);
        new RetryConfig(maxAttempts: 0);
    }
}
