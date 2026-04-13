<?php

declare(strict_types=1);

namespace OpenRouter;

use Closure;
use Exception;
use GuzzleHttp\Client as GuzzleClient;
use Http\Discovery\Psr18ClientDiscovery;
use OpenRouter\Transporters\HttpTransporter;
use OpenRouter\Transporters\RetryConfig;
use Psr\Log\LoggerInterface;
use OpenRouter\ValueObjects\ApiKey;
use OpenRouter\ValueObjects\Transporter\BaseUri;
use OpenRouter\ValueObjects\Transporter\Headers;
use OpenRouter\ValueObjects\Transporter\QueryParams;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class Factory
{
    private const DEFAULT_BASE_URI = 'https://openrouter.ai/api/v1';

    private ?string $apiKey = null;

    private ?string $baseUri = null;

    private ?string $httpReferer = null;

    private ?string $appTitle = null;

    private ?string $appCategories = null;

    private ?string $sessionId = null;

    private ?ClientInterface $httpClient = null;

    /**
     * @var array<string, string>
     */
    private array $headers = [];

    /**
     * @var array<string, string|int>
     */
    private array $queryParams = [];

    private ?Closure $streamHandler = null;

    private ?RetryConfig $retryConfig = null;

    private ?LoggerInterface $logger = null;

    public function withApiKey(string $apiKey): self
    {
        $this->apiKey = trim($apiKey);

        return $this;
    }

    /**
     * Overrides the default base URI (`https://openrouter.ai/api/v1`). Use e.g.
     * `https://eu.openrouter.ai/api/v1` for EU in-region routing.
     */
    public function withBaseUri(string $baseUri): self
    {
        $this->baseUri = $baseUri;

        return $this;
    }

    public function withHttpClient(ClientInterface $client): self
    {
        $this->httpClient = $client;

        return $this;
    }

    public function withStreamHandler(Closure $streamHandler): self
    {
        $this->streamHandler = $streamHandler;

        return $this;
    }

    /**
     * Enables the opt-in retry policy described by OpenRouter's `x-retry-strategy`
     * (exponential backoff on 5XX responses and connection errors). Pass a
     * pre-built {@see RetryConfig} for full control, or omit it to use the
     * spec defaults (3 attempts, 500ms initial delay, 60s cap, multiplier 1.5).
     *
     * Retries apply to the connection-establishment phase of streaming requests
     * only; mid-stream failures are not retried because the emitted deltas are
     * non-idempotent.
     */
    public function withRetry(?RetryConfig $retryConfig = null): self
    {
        $this->retryConfig = $retryConfig ?? new RetryConfig();

        return $this;
    }

    /**
     * Wires a PSR-3 logger. Currently emits a `warning` before each retry
     * attempt and an `error` on terminal request failure. Pass a non-blocking
     * logger — synchronous slow loggers will amplify retry latency.
     */
    public function withLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    public function withHttpHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;

        return $this;
    }

    public function withQueryParam(string $name, string|int $value): self
    {
        $this->queryParams[$name] = $value;

        return $this;
    }

    /**
     * Sets the `HTTP-Referer` header. OpenRouter uses this as the primary identifier for
     * your application in rankings and analytics. Typically this is your app's public URL.
     */
    public function withHttpReferer(string $referer): self
    {
        $this->httpReferer = $referer;

        return $this;
    }

    /**
     * Sets the `X-Title` header. OpenRouter uses this as the display name for your
     * application in its dashboard.
     */
    public function withAppTitle(string $title): self
    {
        $this->appTitle = $title;

        return $this;
    }

    /**
     * Sets the `X-OpenRouter-Categories` header. Accepts either a comma-separated string
     * (`"cli-agent,cloud-agent"`) or an array of strings (`['cli-agent', 'cloud-agent']`).
     */
    public function withAppCategories(string|array $categories): self
    {
        $this->appCategories = is_array($categories) ? implode(',', $categories) : $categories;

        return $this;
    }

    /**
     * Sets the `x-session-id` header. OpenRouter uses this to group related requests.
     * When a request body also carries `session_id`, the body value takes precedence.
     */
    public function withSessionId(string $sessionId): self
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    public function make(): Client
    {
        $headers = Headers::create();

        if ($this->apiKey !== null) {
            $headers = Headers::withAuthorization(ApiKey::from($this->apiKey));
        }

        if ($this->httpReferer !== null) {
            $headers = $headers->withHttpReferer($this->httpReferer);
        }

        if ($this->appTitle !== null) {
            $headers = $headers->withAppTitle($this->appTitle);
        }

        if ($this->appCategories !== null) {
            $headers = $headers->withAppCategories($this->appCategories);
        }

        if ($this->sessionId !== null) {
            $headers = $headers->withSessionId($this->sessionId);
        }

        foreach ($this->headers as $name => $value) {
            $headers = $headers->withCustomHeader($name, $value);
        }

        $baseUri = BaseUri::from($this->baseUri ?: self::DEFAULT_BASE_URI);

        $queryParams = QueryParams::create();
        foreach ($this->queryParams as $name => $value) {
            $queryParams = $queryParams->withParam($name, $value);
        }

        $client = $this->httpClient ??= Psr18ClientDiscovery::find();

        $sendAsync = $this->makeStreamHandler($client);

        $transporter = new HttpTransporter(
            $client,
            $baseUri,
            $headers,
            $queryParams,
            $sendAsync,
            $this->retryConfig,
            $this->logger,
        );

        return new Client($transporter);
    }

    private function makeStreamHandler(ClientInterface $client): Closure
    {
        if ($this->streamHandler !== null) {
            return $this->streamHandler;
        }

        if ($client instanceof GuzzleClient) {
            return fn (RequestInterface $request): ResponseInterface => $client->send($request, ['stream' => true]);
        }

        return function (RequestInterface $request) use ($client): ResponseInterface {
            return $client->sendRequest($request);
        };
    }
}
