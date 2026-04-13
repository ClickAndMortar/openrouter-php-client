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
     */
    private function __construct(
        private readonly ContentType $contentType,
        private readonly Method $method,
        private readonly ResourceUri $uri,
        private readonly array $parameters = [],
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

    public function toRequest(BaseUri $baseUri, Headers $headers, QueryParams $queryParams): RequestInterface
    {
        $requestFactory = Psr17FactoryDiscovery::findRequestFactory();
        $streamFactory = Psr17FactoryDiscovery::findStreamFactory();

        $uri = $baseUri->toString().$this->uri->toString();

        $query = $queryParams->toArray();
        if ($this->method === Method::GET) {
            $query = [...$query, ...$this->parameters];
        }

        if ($query !== []) {
            $uri .= '?'.http_build_query($query);
        }

        $headers = $headers->withContentType($this->contentType);

        $body = null;
        if ($this->method === Method::POST) {
            $body = $streamFactory->createStream(
                json_encode($this->parameters, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
            );
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
}
