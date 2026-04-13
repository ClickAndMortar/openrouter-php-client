<?php

declare(strict_types=1);

namespace OpenRouter\Responses\Models;

/**
 * @phpstan-import-type ListResponseModelArchitectureType from ListResponseModelArchitecture
 * @phpstan-import-type ListResponseModelPricingType from ListResponseModelPricing
 * @phpstan-import-type ListResponseModelTopProviderType from ListResponseModelTopProvider
 *
 * @phpstan-type ListResponseModelType array{
 *     id: string,
 *     canonical_slug: string,
 *     name: string,
 *     created: int,
 *     description?: string,
 *     context_length: int,
 *     architecture: ListResponseModelArchitectureType,
 *     pricing: ListResponseModelPricingType,
 *     top_provider: ListResponseModelTopProviderType,
 *     supported_parameters?: array<int, string>,
 *     per_request_limits?: array<string, mixed>|null,
 *     default_parameters?: array<string, mixed>|null,
 *     knowledge_cutoff?: string|null,
 *     expiration_date?: string|null,
 *     hugging_face_id?: string|null,
 *     links?: array{details: string},
 * }
 */
final class ListResponseModel
{
    /**
     * @param  array<int, string>  $supportedParameters
     * @param  array<string, mixed>|null  $perRequestLimits
     * @param  array<string, mixed>|null  $defaultParameters
     * @param  array{details?: string}|null  $links
     */
    private function __construct(
        public readonly string $id,
        public readonly string $canonicalSlug,
        public readonly string $name,
        public readonly int $created,
        public readonly ?string $description,
        public readonly int $contextLength,
        public readonly ListResponseModelArchitecture $architecture,
        public readonly ListResponseModelPricing $pricing,
        public readonly ListResponseModelTopProvider $topProvider,
        public readonly array $supportedParameters,
        public readonly ?array $perRequestLimits,
        public readonly ?array $defaultParameters,
        public readonly ?string $knowledgeCutoff,
        public readonly ?string $expirationDate,
        public readonly ?string $huggingFaceId,
        public readonly ?array $links,
    ) {
    }

    /**
     * @param  ListResponseModelType  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            id: $attributes['id'],
            canonicalSlug: $attributes['canonical_slug'],
            name: $attributes['name'],
            created: $attributes['created'],
            description: $attributes['description'] ?? null,
            contextLength: $attributes['context_length'],
            architecture: ListResponseModelArchitecture::from($attributes['architecture']),
            pricing: ListResponseModelPricing::from($attributes['pricing']),
            topProvider: ListResponseModelTopProvider::from($attributes['top_provider']),
            supportedParameters: $attributes['supported_parameters'] ?? [],
            perRequestLimits: $attributes['per_request_limits'] ?? null,
            defaultParameters: $attributes['default_parameters'] ?? null,
            knowledgeCutoff: $attributes['knowledge_cutoff'] ?? null,
            expirationDate: $attributes['expiration_date'] ?? null,
            huggingFaceId: $attributes['hugging_face_id'] ?? null,
            links: $attributes['links'] ?? null,
        );
    }

    /**
     * @return ListResponseModelType
     */
    public function toArray(): array
    {
        $data = [
            'id' => $this->id,
            'canonical_slug' => $this->canonicalSlug,
            'name' => $this->name,
            'created' => $this->created,
            'context_length' => $this->contextLength,
            'architecture' => $this->architecture->toArray(),
            'pricing' => $this->pricing->toArray(),
            'top_provider' => $this->topProvider->toArray(),
            'supported_parameters' => $this->supportedParameters,
            'per_request_limits' => $this->perRequestLimits,
            'default_parameters' => $this->defaultParameters,
            'knowledge_cutoff' => $this->knowledgeCutoff,
            'expiration_date' => $this->expirationDate,
            'links' => $this->links,
        ];

        if ($this->description !== null) {
            $data['description'] = $this->description;
        }

        if ($this->huggingFaceId !== null) {
            $data['hugging_face_id'] = $this->huggingFaceId;
        }

        /** @var ListResponseModelType $data */
        return $data;
    }
}
