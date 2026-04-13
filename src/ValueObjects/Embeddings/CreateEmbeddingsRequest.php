<?php

declare(strict_types=1);

namespace OpenRouter\ValueObjects\Embeddings;

use OpenRouter\Enums\Embeddings\EncodingFormat;
use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\ValueObjects\Responses\Config\ProviderPreferences;

/**
 * Typed builder for a `POST /embeddings` request. Mirrors the embeddings
 * request schema. Validates that `model` and `input` are present and
 * non-empty at construction time and serializes via {@see toArray()}.
 *
 * `input` is intentionally `mixed` because the API accepts five shapes: a
 * single string, a list of strings, a list of token ids, a list of token id
 * lists, or a list of multimodal content objects. Unknown fields pass through
 * `$extras` unchanged.
 */
final class CreateEmbeddingsRequest
{
    /**
     * @param  string|list<string>|list<int>|list<list<int>>|list<array<string, mixed>>  $input
     * @param  ProviderPreferences|array<string, mixed>|null  $provider
     * @param  array<string, mixed>  $extras
     */
    public function __construct(
        public readonly mixed $input,
        public readonly string $model,
        public readonly ?int $dimensions = null,
        public readonly EncodingFormat|string|null $encodingFormat = null,
        public readonly ?string $inputType = null,
        public readonly mixed $provider = null,
        public readonly ?string $user = null,
        public readonly array $extras = [],
    ) {
        if ($this->model === '') {
            throw new InvalidArgumentException('CreateEmbeddingsRequest::$model must not be an empty string');
        }

        if (is_string($this->input) && $this->input === '') {
            throw new InvalidArgumentException('CreateEmbeddingsRequest::$input must not be an empty string');
        }

        if (is_array($this->input) && $this->input === []) {
            throw new InvalidArgumentException('CreateEmbeddingsRequest::$input must not be an empty array');
        }

        if (! is_string($this->input) && ! is_array($this->input)) {
            throw new InvalidArgumentException('CreateEmbeddingsRequest::$input must be a string or array');
        }

        if ($this->dimensions !== null && $this->dimensions <= 0) {
            throw new InvalidArgumentException('CreateEmbeddingsRequest::$dimensions must be greater than 0');
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'input' => $this->input,
            'model' => $this->model,
        ];

        if ($this->dimensions !== null) {
            $data['dimensions'] = $this->dimensions;
        }

        if ($this->encodingFormat !== null) {
            $data['encoding_format'] = $this->encodingFormat instanceof EncodingFormat
                ? $this->encodingFormat->value
                : $this->encodingFormat;
        }

        if ($this->inputType !== null) {
            $data['input_type'] = $this->inputType;
        }

        if ($this->provider !== null) {
            $data['provider'] = $this->provider instanceof ProviderPreferences
                ? $this->provider->toArray()
                : $this->provider;
        }

        if ($this->user !== null) {
            $data['user'] = $this->user;
        }

        foreach ($this->extras as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }
}
