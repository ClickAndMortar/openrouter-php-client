<?php

declare(strict_types=1);

namespace OpenRouter\Responses;

use Generator;
use JsonException;
use OpenRouter\Contracts\ResponseHasMetaInformationContract;
use OpenRouter\Contracts\ResponseStreamContract;
use OpenRouter\Exceptions\ErrorException;
use OpenRouter\Responses\Meta\MetaInformation;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Iterates over OpenRouter's `text/event-stream` body for `/responses` and yields
 * one `$responseClass::from($payload)` value per SSE `data:` frame. Respects the
 * `[DONE]` sentinel and decodes server-sent `error:` frames into `ErrorException`.
 *
 * @template TResponse
 *
 * @implements ResponseStreamContract<TResponse>
 */
final class StreamResponse implements ResponseStreamContract, ResponseHasMetaInformationContract
{
    /**
     * @param  class-string<TResponse>  $responseClass
     */
    public function __construct(
        private readonly string $responseClass,
        private readonly ResponseInterface $response,
    ) {
    }

    public function getIterator(): Generator
    {
        while (! $this->response->getBody()->eof()) {
            $line = $this->readLine($this->response->getBody());

            $event = null;
            if (str_starts_with($line, 'event:')) {
                $event = trim(substr($line, strlen('event:')));
                $line = $this->readLine($this->response->getBody());
            }

            if (! str_starts_with($line, 'data:')) {
                continue;
            }

            $data = trim(substr($line, strlen('data:')));

            if ($data === '[DONE]') {
                break;
            }

            try {
                /** @var array<string, mixed> $payload */
                $payload = json_decode($data, true, flags: JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                continue;
            }

            if (isset($payload['error']) && is_array($payload['error'])) {
                /** @var array{message: string|array<int, string>, type?: string, code?: string|int} $error */
                $error = $payload['error'];
                throw new ErrorException($error, $this->response);
            }

            if ($event !== null) {
                $payload['__event'] = $event;
            }

            yield $this->responseClass::from($payload);
        }
    }

    public function meta(): MetaInformation
    {
        return MetaInformation::from($this->response->getHeaders());
    }

    private function readLine(StreamInterface $stream): string
    {
        $buffer = '';

        while (! $stream->eof()) {
            if (($byte = $stream->read(1)) === '') {
                return $buffer;
            }
            $buffer .= $byte;
            if ($byte === "\n") {
                break;
            }
        }

        return $buffer;
    }
}
