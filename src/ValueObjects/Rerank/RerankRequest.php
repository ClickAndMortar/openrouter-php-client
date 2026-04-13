<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Rerank;

use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\ValueObjects\Responses\Config\ProviderPreferences;

/**
 * Typed builder for a `POST /rerank` request. Validates that `model`,
 * `query`, and `documents` are present and non-empty at construction time.
 */
final class RerankRequest
{
    /**
     * @param  list<string>  $documents
     * @param  ProviderPreferences|array<string, mixed>|null  $provider
     * @param  array<string, mixed>  $extras
     */
    public function __construct(
        public readonly string $model,
        public readonly string $query,
        public readonly array $documents,
        public readonly ?int $topN = null,
        public readonly mixed $provider = null,
        public readonly array $extras = [],
    ) {
        if ($this->model === '') {
            throw new InvalidArgumentException('RerankRequest::$model must not be an empty string');
        }

        if ($this->query === '') {
            throw new InvalidArgumentException('RerankRequest::$query must not be an empty string');
        }

        if ($this->documents === []) {
            throw new InvalidArgumentException('RerankRequest::$documents must not be an empty array');
        }

        if ($this->topN !== null && $this->topN <= 0) {
            throw new InvalidArgumentException('RerankRequest::$topN must be greater than 0');
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'model' => $this->model,
            'query' => $this->query,
            'documents' => $this->documents,
        ];

        if ($this->topN !== null) {
            $data['top_n'] = $this->topN;
        }

        if ($this->provider !== null) {
            $data['provider'] = $this->provider instanceof ProviderPreferences
                ? $this->provider->toArray()
                : $this->provider;
        }

        foreach ($this->extras as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }
}
