<?php

declare(strict_types=1);

namespace OpenRouter\Transporters;

use Closure;
use GuzzleHttp\Exception\ClientException;
use JsonException;
use OpenRouter\Contracts\TransporterContract;
use OpenRouter\Enums\Transporter\ContentType;
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
use OpenRouter\Exceptions\TransporterException;
use OpenRouter\Exceptions\UnserializableResponse;
use OpenRouter\ValueObjects\Transporter\BaseUri;
use OpenRouter\ValueObjects\Transporter\Headers;
use OpenRouter\ValueObjects\Transporter\Payload;
use OpenRouter\ValueObjects\Transporter\QueryParams;
use OpenRouter\ValueObjects\Transporter\Response;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * @internal
 */
final class HttpTransporter implements TransporterContract
{
    public function __construct(
        private readonly ClientInterface $client,
        private readonly BaseUri $baseUri,
        private Headers $headers,
        private readonly QueryParams $queryParams,
        private readonly Closure $streamHandler,
        private readonly ?RetryConfig $retryConfig = null,
        private readonly ?LoggerInterface $logger = null,
    ) {
    }

    public function addHeader(string $name, string $value): self
    {
        $this->headers = $this->headers->withCustomHeader($name, $value);

        return $this;
    }

    public function requestObject(Payload $payload): Response
    {
        $request = $payload->toRequest($this->baseUri, $this->headers, $this->queryParams);

        $response = $this->sendRequest(fn (): ResponseInterface => $this->client->sendRequest($request));

        $contents = (string) $response->getBody();

        $this->throwIfJsonError($response, $contents);

        try {
            /** @var array{error?: array{message: string, type?: string, code?: string|int}} $data */
            $data = json_decode($contents, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $jsonException) {
            throw new UnserializableResponse($jsonException, $response);
        }

        return Response::from($data, $response->getHeaders());
    }

    public function requestStream(Payload $payload): ResponseInterface
    {
        $request = $payload->toRequest($this->baseUri, $this->headers, $this->queryParams);

        // Retries apply to connection establishment only — once the stream
        // opens successfully, mid-stream failures cannot be safely retried
        // because emitted deltas are non-idempotent.
        $response = $this->sendRequest(fn (): ResponseInterface => ($this->streamHandler)($request));

        $this->throwIfJsonError($response, $response);

        return $response;
    }

    private function sendRequest(Closure $callable): ResponseInterface
    {
        $config = $this->retryConfig;
        $maxAttempts = $config?->maxAttempts ?? 1;
        $lastException = null;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            if ($config !== null && $attempt > 1) {
                ($config->sleeper)($config->delayForAttempt($attempt));
            }

            try {
                $response = $callable();
            } catch (ClientExceptionInterface $clientException) {
                $lastException = $clientException;

                if ($clientException instanceof ClientException) {
                    $errorResponse = $clientException->getResponse();
                    $status = $errorResponse->getStatusCode();

                    if ($status < 500 || $config === null || $attempt >= $maxAttempts) {
                        $this->logError($status, $attempt);
                        $this->throwIfJsonError($errorResponse, (string) $errorResponse->getBody());

                        throw new TransporterException($clientException);
                    }

                    $this->logRetry($attempt, $maxAttempts, $status, null, $config->delayForAttempt($attempt + 1));
                    continue;
                }

                if ($config === null || $attempt >= $maxAttempts) {
                    $this->logError(null, $attempt, $clientException);

                    throw new TransporterException($clientException);
                }

                $this->logRetry($attempt, $maxAttempts, null, $clientException::class, $config->delayForAttempt($attempt + 1));
                continue;
            }

            $status = $response->getStatusCode();

            if ($status >= 500 && $config !== null && $attempt < $maxAttempts) {
                $this->logRetry($attempt, $maxAttempts, $status, null, $config->delayForAttempt($attempt + 1));
                continue;
            }

            if ($status >= 400) {
                $this->logError($status, $attempt);
            }

            return $response;
        }

        $this->logError(null, $maxAttempts, $lastException);

        throw new TransporterException($lastException);
    }

    private function logRetry(int $attempt, int $maxAttempts, ?int $status, ?string $exception, int $delayMs): void
    {
        $this->logger?->warning('OpenRouter request retrying', [
            'attempt' => $attempt,
            'max_attempts' => $maxAttempts,
            'status' => $status,
            'exception' => $exception,
            'delay_ms' => $delayMs,
        ]);
    }

    private function logError(?int $status, int $attempts, ?Throwable $exception = null): void
    {
        if ($status !== null && $status < 400) {
            return;
        }

        $this->logger?->error('OpenRouter request failed', [
            'status' => $status,
            'attempts' => $attempts,
            'exception' => $exception !== null ? $exception::class : null,
            'message' => $exception?->getMessage(),
        ]);
    }

    private function throwIfJsonError(ResponseInterface $response, string|ResponseInterface $contents): void
    {
        if ($response->getStatusCode() < 400) {
            return;
        }

        if ($contents instanceof ResponseInterface) {
            $contents = (string) $contents->getBody();
        }

        try {
            /** @var array{error?: string|array{message: string|array<int, string>, type?: string, code?: string|int}} $data */
            $data = json_decode($contents, true, flags: JSON_THROW_ON_ERROR);

            if (isset($data['error'])) {
                throw self::makeHttpException($data['error'], $response);
            }
        } catch (JsonException $jsonException) {
            if (! str_contains($response->getHeaderLine('Content-Type'), ContentType::JSON->value)) {
                return;
            }

            throw new UnserializableResponse($jsonException, $response);
        }
    }

    /**
     * Dispatches an upstream `error` payload to the most specific
     * {@see ErrorException} subclass for the given HTTP status. Unknown
     * status codes fall back to the base class.
     *
     * @param  array{message?: string|array<int, string>, type?: ?string, code?: string|int|null, metadata?: array<string, mixed>}|string  $error
     */
    private static function makeHttpException(string|array $error, ResponseInterface $response): ErrorException
    {
        return match ($response->getStatusCode()) {
            400 => new BadRequestException($error, $response),
            401 => new UnauthorizedException($error, $response),
            402 => new PaymentRequiredException($error, $response),
            404 => new NotFoundException($error, $response),
            408 => new RequestTimeoutException($error, $response),
            413 => new PayloadTooLargeException($error, $response),
            422 => new UnprocessableEntityException($error, $response),
            429 => new TooManyRequestsException($error, $response),
            500 => new InternalServerErrorException($error, $response),
            502 => new BadGatewayException($error, $response),
            503 => new ServiceUnavailableException($error, $response),
            524 => new OriginTimeoutException($error, $response),
            529 => new OriginOverloadedException($error, $response),
            default => new ErrorException($error, $response),
        };
    }
}
