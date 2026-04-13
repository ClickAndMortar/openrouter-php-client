<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Exceptions;

use OpenRouter\Exceptions\ErrorException;
use OpenRouter\Exceptions\Http\BadGatewayException;
use OpenRouter\Exceptions\Http\BadRequestException;
use OpenRouter\Exceptions\Http\InternalServerErrorException;
use OpenRouter\Exceptions\Http\NotFoundException;
use OpenRouter\Exceptions\Http\OriginOverloadedException;
use OpenRouter\Exceptions\Http\OriginTimeoutException;
use OpenRouter\Exceptions\Http\PaymentRequiredException;
use OpenRouter\Exceptions\Http\PayloadTooLargeException;
use OpenRouter\Exceptions\Http\RequestTimeoutException;
use OpenRouter\Exceptions\Http\ServiceUnavailableException;
use OpenRouter\Exceptions\Http\TooManyRequestsException;
use OpenRouter\Exceptions\Http\UnauthorizedException;
use OpenRouter\Exceptions\Http\UnprocessableEntityException;
use OpenRouter\Factory;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use PHPUnit\Framework\TestCase;
use Throwable;

final class HttpExceptionDispatchTest extends TestCase
{
    /**
     * @return list<array{int, class-string<ErrorException>}>
     */
    public static function statusCases(): array
    {
        return [
            [400, BadRequestException::class],
            [401, UnauthorizedException::class],
            [402, PaymentRequiredException::class],
            [404, NotFoundException::class],
            [408, RequestTimeoutException::class],
            [413, PayloadTooLargeException::class],
            [422, UnprocessableEntityException::class],
            [429, TooManyRequestsException::class],
            [500, InternalServerErrorException::class],
            [502, BadGatewayException::class],
            [503, ServiceUnavailableException::class],
            [524, OriginTimeoutException::class],
            [529, OriginOverloadedException::class],
        ];
    }

    /**
     * @param  class-string<ErrorException>  $expected
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('statusCases')]
    public function testDispatchesToTypedSubclass(int $status, string $expected): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson([
            'error' => ['message' => 'boom', 'type' => 'invalid_request_error', 'code' => 'bad'],
        ], $status);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $thrown = null;
        try {
            $client->responses()->send(['model' => 'm', 'input' => 'hi']);
        } catch (Throwable $e) {
            $thrown = $e;
        }

        $this->assertInstanceOf($expected, $thrown);
        $this->assertInstanceOf(ErrorException::class, $thrown);
        /** @var ErrorException $thrown */
        $this->assertSame($status, $thrown->getStatusCode());
        $this->assertSame('boom', $thrown->getMessage());
        $this->assertSame('invalid_request_error', $thrown->getErrorType());
        $this->assertSame('bad', $thrown->getErrorCode());
    }

    public function testUnknownStatusFallsBackToBaseClass(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['error' => ['message' => 'odd']], 418);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $thrown = null;
        try {
            $client->responses()->send(['model' => 'm', 'input' => 'hi']);
        } catch (Throwable $e) {
            $thrown = $e;
        }

        $this->assertInstanceOf(ErrorException::class, $thrown);
        $this->assertSame(ErrorException::class, $thrown::class);
    }

    public function testMetadataIsPreserved(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson([
            'error' => [
                'message' => 'upstream busted',
                'metadata' => ['provider_name' => 'anthropic', 'raw' => ['x' => 1]],
            ],
        ], 502);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $thrown = null;
        try {
            $client->responses()->send(['model' => 'm', 'input' => 'hi']);
        } catch (Throwable $e) {
            $thrown = $e;
        }

        $this->assertInstanceOf(BadGatewayException::class, $thrown);
        /** @var BadGatewayException $thrown */
        $this->assertSame(['provider_name' => 'anthropic', 'raw' => ['x' => 1]], $thrown->getMetadata());
    }
}
