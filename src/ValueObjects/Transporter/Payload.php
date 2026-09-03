<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Transporter;

use Http\Discovery\Psr17FactoryDiscovery;
use OpenRouter\Enums\Transporter\ContentType;
use OpenRouter\Enums\Transporter\Method;
use OpenRouter\ValueObjects\ResourceUri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

final class Payload
{
    /**
     * @param  array<string, mixed>  $parameters
     * @param  array<string, mixed>  $query  Query string parameters, for methods whose
     *                                       `$parameters` are carried in the body.
     */
    private function __construct(
        private readonly ContentType $contentType,
        private readonly Method $method,
        private readonly ResourceUri $uri,
        private readonly array $parameters = [],
        private readonly array $query = [],
    ) {
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function list(string $resource, array $parameters = []): self
    {
        return new self(ContentType::JSON, Method::GET, ResourceUri::list($resource), $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function retrieve(string $resource, string $id, string $suffix = '', array $parameters = []): self
    {
        return new self(ContentType::JSON, Method::GET, ResourceUri::retrieve($resource, $id, $suffix), $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function create(string $resource, array $parameters): self
    {
        return new self(ContentType::JSON, Method::POST, ResourceUri::create($resource), $parameters);
    }

    /**
     * @param  array<string, mixed>  $query
     */
    public static function delete(string $resource, string $id = '', string $suffix = '', array $query = []): self
    {
        $uri = $id === ''
            ? ResourceUri::list($resource)
            : ResourceUri::retrieve($resource, $id, $suffix);

        return new self(ContentType::JSON, Method::DELETE, $uri, [], $query);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function modify(string $resource, string $id, array $parameters, string $suffix = ''): self
    {
        return new self(ContentType::JSON, Method::PATCH, ResourceUri::retrieve($resource, $id, $suffix), $parameters);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function put(string $resource, array $parameters): self
    {
        return new self(ContentType::JSON, Method::PUT, ResourceUri::create($resource), $parameters);
    }

    /**
     * Builds a `multipart/form-data` upload.
     *
     * `$fields` maps each form field to an {@see UploadedFile}, a scalar, or a
     * list of scalars (sent as repeated `name[]` parts, which is how the API
     * expects `timestamp_granularities`).
     *
     * @param  array<string, UploadedFile|scalar|array<int, scalar>>  $fields
     * @param  array<string, mixed>  $query
     */
    public static function upload(string $resource, array $fields, array $query = []): self
    {
        return new self(ContentType::MULTIPART, Method::POST, ResourceUri::create($resource), $fields, $query);
    }

    public function toRequest(BaseUri $baseUri, Headers $headers, QueryParams $queryParams): RequestInterface
    {
        $requestFactory = Psr17FactoryDiscovery::findRequestFactory();
        $streamFactory = Psr17FactoryDiscovery::findStreamFactory();

        $uri = $baseUri->toString().$this->uri->toString();

        $query = [...$queryParams->toArray(), ...$this->query];
        if ($this->method === Method::GET) {
            $query = [...$query, ...$this->parameters];
        }

        if ($query !== []) {
            $uri .= '?'.http_build_query($query);
        }

        $body = null;

        if ($this->contentType === ContentType::MULTIPART) {
            $boundary = bin2hex(random_bytes(16));
            $headers = $headers->withContentType($this->contentType, '; boundary='.$boundary);
            $body = $streamFactory->createStream($this->encodeMultipart($boundary));
        } else {
            $headers = $headers->withContentType($this->contentType);

            if ($this->method === Method::POST || $this->method === Method::PATCH || $this->method === Method::PUT) {
                $body = $streamFactory->createStream(
                    json_encode($this->parameters, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
                );
            }
        }

        $request = $requestFactory->createRequest($this->method->value, $uri);

        if ($body instanceof StreamInterface) {
            $request = $request->withBody($body);
        }

        foreach ($headers->toArray() as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        return $request;
    }

    /**
     * RFC 7578 body. Files carry a filename and their own content type; scalars
     * are sent bare; lists become repeated `name[]` parts.
     */
    private function encodeMultipart(string $boundary): string
    {
        $body = '';

        foreach ($this->parameters as $name => $value) {
            if ($value instanceof UploadedFile) {
                $body .= "--{$boundary}\r\n"
                    ."Content-Disposition: form-data; name=\"{$name}\"; filename=\"{$value->filename}\"\r\n"
                    ."Content-Type: {$value->contentType}\r\n\r\n"
                    .$value->contents."\r\n";

                continue;
            }

            if (is_array($value)) {
                foreach ($value as $item) {
                    $body .= "--{$boundary}\r\n"
                        ."Content-Disposition: form-data; name=\"{$name}[]\"\r\n\r\n"
                        .self::stringify($item)."\r\n";
                }

                continue;
            }

            $body .= "--{$boundary}\r\n"
                ."Content-Disposition: form-data; name=\"{$name}\"\r\n\r\n"
                .self::stringify($value)."\r\n";
        }

        return $body."--{$boundary}--\r\n";
    }

    private static function stringify(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        return (string) $value;   // @phpstan-ignore-line — multipart fields are scalars
    }
}
