<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Transporters;

use OpenRouter\Exceptions\Http\InternalServerErrorException;
use OpenRouter\Exceptions\Http\UnauthorizedException;
use OpenRouter\Factory;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use OpenRouter\Tests\Doubles\SpyLogger;
use OpenRouter\Tests\Fixtures\ResponsesCreateFixture;
use OpenRouter\Transporters\RetryConfig;
use PHPUnit\Framework\TestCase;

final class HttpTransporterLoggingTest extends TestCase
{
    public function testSuccessDoesNotLog(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ResponsesCreateFixture::ATTRIBUTES, 200);
        $logger = new SpyLogger();

        $client = (new Factory())
            ->withApiKey('sk-test')
            ->withHttpClient($http)
            ->withLogger($logger)
            ->make();

        $client->responses()->send(['model' => 'm', 'input' => 'i']);

        $this->assertSame([], $logger->records);
    }

    public function testRetryThenSuccessLogsWarningOnly(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['error' => ['message' => 'boom']], 500);
        $http->enqueueJson(ResponsesCreateFixture::ATTRIBUTES, 200);
        $logger = new SpyLogger();

        $client = (new Factory())
            ->withApiKey('sk-test')
            ->withHttpClient($http)
            ->withRetry(new RetryConfig(maxAttempts: 3, initialDelayMs: 1, sleeper: static fn () => null))
            ->withLogger($logger)
            ->make();

        $client->responses()->send(['model' => 'm', 'input' => 'i']);

        $warnings = $logger->byLevel('warning');
        $this->assertCount(1, $warnings);
        $this->assertSame('OpenRouter request retrying', $warnings[0]['message']);
        $this->assertSame(1, $warnings[0]['context']['attempt']);
        $this->assertSame(3, $warnings[0]['context']['max_attempts']);
        $this->assertSame(500, $warnings[0]['context']['status']);
        $this->assertSame([], $logger->byLevel('error'));
    }

    public function testRetryExhaustedLogsWarningsThenError(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['error' => ['message' => 'boom']], 503);
        $http->enqueueJson(['error' => ['message' => 'boom']], 503);
        $logger = new SpyLogger();

        $client = (new Factory())
            ->withApiKey('sk-test')
            ->withHttpClient($http)
            ->withRetry(new RetryConfig(maxAttempts: 2, initialDelayMs: 1, sleeper: static fn () => null))
            ->withLogger($logger)
            ->make();

        try {
            $client->responses()->send(['model' => 'm', 'input' => 'i']);
            $this->fail('expected ServiceUnavailableException');
        } catch (\OpenRouter\Exceptions\Http\ServiceUnavailableException) {
            // expected
        }

        $this->assertCount(1, $logger->byLevel('warning'));
        $errors = $logger->byLevel('error');
        $this->assertCount(1, $errors);
        $this->assertSame(503, $errors[0]['context']['status']);
        $this->assertSame(2, $errors[0]['context']['attempts']);
    }

    public function testNonRetryableStatusLogsErrorOnly(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['error' => ['message' => 'unauth']], 401);
        $logger = new SpyLogger();

        $client = (new Factory())
            ->withApiKey('sk-test')
            ->withHttpClient($http)
            ->withRetry(new RetryConfig(maxAttempts: 3, initialDelayMs: 1, sleeper: static fn () => null))
            ->withLogger($logger)
            ->make();

        try {
            $client->responses()->send(['model' => 'm', 'input' => 'i']);
            $this->fail('expected UnauthorizedException');
        } catch (UnauthorizedException) {
            // expected
        }

        $this->assertSame([], $logger->byLevel('warning'));
        $errors = $logger->byLevel('error');
        $this->assertCount(1, $errors);
        $this->assertSame(401, $errors[0]['context']['status']);
        $this->assertSame(1, $errors[0]['context']['attempts']);
    }

    public function testErrorWithoutRetryLogs(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['error' => ['message' => 'boom']], 500);
        $logger = new SpyLogger();

        $client = (new Factory())
            ->withApiKey('sk-test')
            ->withHttpClient($http)
            ->withLogger($logger)
            ->make();

        try {
            $client->responses()->send(['model' => 'm', 'input' => 'i']);
            $this->fail('expected InternalServerErrorException');
        } catch (InternalServerErrorException) {
            // expected
        }

        $this->assertSame([], $logger->byLevel('warning'));
        $this->assertCount(1, $logger->byLevel('error'));
    }
}
