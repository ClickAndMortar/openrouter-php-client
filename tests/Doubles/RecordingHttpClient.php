<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Doubles;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

/**
 * A minimal PSR-18 client for tests. Captures every outgoing request and returns
 * the next queued canned response. Use {@see enqueue()} to set up the response stack.
 */
final class RecordingHttpClient implements ClientInterface
{
    /** @var list<RequestInterface> */
    public array $requests = [];

    /** @var list<ResponseInterface> */
    private array $responses = [];

    public function enqueueJson(array $data, int $statusCode = 200, array $headers = []): void
    {
        $body = json_encode($data, JSON_THROW_ON_ERROR);
        $this->responses[] = new Response(
            $statusCode,
            array_merge(['Content-Type' => 'application/json'], $headers),
            $body,
        );
    }

    public function enqueueStream(string $sseBody, int $statusCode = 200, array $headers = []): void
    {
        $this->responses[] = new Response(
            $statusCode,
            array_merge(['Content-Type' => 'text/event-stream'], $headers),
            $sseBody,
        );
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $this->requests[] = $request;

        if ($this->responses === []) {
            throw new RuntimeException('No canned response enqueued for '.$request->getMethod().' '.$request->getUri());
        }

        return array_shift($this->responses);
    }

    public function lastRequest(): RequestInterface
    {
        if ($this->requests === []) {
            throw new RuntimeException('No requests captured.');
        }

        return $this->requests[count($this->requests) - 1];
    }
}
