<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Responses;

use OpenRouter\Responses\Activity\ActivityItem;
use OpenRouter\Responses\Chat\ChatChoice;
use OpenRouter\Responses\Chat\ChatResponseMessage;
use OpenRouter\Responses\Chat\ChatUsage;
use OpenRouter\Responses\Credits\CreditsData;
use OpenRouter\Responses\Embeddings\EmbeddingData;
use OpenRouter\Responses\Generation\GenerationData;
use OpenRouter\Responses\Guardrails\Guardrail;
use OpenRouter\Responses\Keys\ApiKeyDetail;
use OpenRouter\Responses\Keys\ApiKeyInfo;
use OpenRouter\Responses\Models\ListEndpointsResponseEndpoint;
use OpenRouter\Responses\Models\ListEndpointsResponseModel;
use OpenRouter\Responses\Models\ListResponseModel;
use OpenRouter\Responses\Models\ListResponseModelArchitecture;
use OpenRouter\Responses\Models\ListResponseModelTopProvider;
use OpenRouter\Responses\Organization\OrganizationMember;
use OpenRouter\Responses\Providers\ProviderItem;
use OpenRouter\Responses\Rerank\RerankResult;
use OpenRouter\Responses\Responses\CreateResponseUsage;
use OpenRouter\Tests\Fixtures\ActivityListFixture;
use OpenRouter\Tests\Fixtures\ChatCreateFixture;
use OpenRouter\Tests\Fixtures\CreditsRetrieveFixture;
use OpenRouter\Tests\Fixtures\EmbeddingsCreateFixture;
use OpenRouter\Tests\Fixtures\GenerationRetrieveFixture;
use OpenRouter\Tests\Fixtures\GuardrailsRetrieveFixture;
use OpenRouter\Tests\Fixtures\KeysCurrentFixture;
use OpenRouter\Tests\Fixtures\KeysListFixture;
use OpenRouter\Tests\Fixtures\ModelsListEndpointsFixture;
use OpenRouter\Tests\Fixtures\ModelsListFixture;
use OpenRouter\Tests\Fixtures\OrganizationMembersFixture;
use OpenRouter\Tests\Fixtures\ProvidersListFixture;
use OpenRouter\Tests\Fixtures\RerankFixture;
use OpenRouter\Tests\Fixtures\ResponsesCreateFixture;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * OpenRouter adds fields to existing objects continuously. Every value object
 * that models an API entity must therefore keep the fields it does not know
 * about, rather than dropping them on the floor, so that callers can reach new
 * API features before the SDK grows typed accessors for them.
 */
final class UnknownFieldPreservationTest extends TestCase
{
    /**
     * @return iterable<string, array{class-string, array<string, mixed>}>
     */
    public static function entityProvider(): iterable
    {
        yield 'Activity/ActivityItem' => [ActivityItem::class, ActivityListFixture::ATTRIBUTES['data'][0]];
        yield 'Chat/ChatChoice' => [ChatChoice::class, ChatCreateFixture::ATTRIBUTES['choices'][0]];
        yield 'Chat/ChatResponseMessage' => [ChatResponseMessage::class, ChatCreateFixture::ATTRIBUTES['choices'][0]['message']];
        yield 'Chat/ChatUsage' => [ChatUsage::class, ChatCreateFixture::ATTRIBUTES['usage']];
        yield 'Credits/CreditsData' => [CreditsData::class, CreditsRetrieveFixture::ATTRIBUTES['data']];
        yield 'Embeddings/EmbeddingData' => [EmbeddingData::class, EmbeddingsCreateFixture::ATTRIBUTES['data'][0]];
        yield 'Generation/GenerationData' => [GenerationData::class, GenerationRetrieveFixture::ATTRIBUTES['data']];
        yield 'Guardrails/Guardrail' => [Guardrail::class, GuardrailsRetrieveFixture::ATTRIBUTES['data']];
        yield 'Keys/ApiKeyDetail' => [ApiKeyDetail::class, KeysCurrentFixture::ATTRIBUTES['data']];
        yield 'Keys/ApiKeyInfo' => [ApiKeyInfo::class, KeysListFixture::ATTRIBUTES['data'][0]];
        yield 'Models/ListEndpointsResponseEndpoint' => [ListEndpointsResponseEndpoint::class, ModelsListEndpointsFixture::ATTRIBUTES['data']['endpoints'][0]];
        yield 'Models/ListEndpointsResponseModel' => [ListEndpointsResponseModel::class, ModelsListEndpointsFixture::ATTRIBUTES['data']];
        yield 'Models/ListResponseModel' => [ListResponseModel::class, ModelsListFixture::ATTRIBUTES['data'][0]];
        yield 'Models/ListResponseModelArchitecture' => [ListResponseModelArchitecture::class, ModelsListFixture::ATTRIBUTES['data'][0]['architecture']];
        yield 'Models/ListResponseModelTopProvider' => [ListResponseModelTopProvider::class, ModelsListFixture::ATTRIBUTES['data'][0]['top_provider']];
        yield 'Organization/OrganizationMember' => [OrganizationMember::class, OrganizationMembersFixture::ATTRIBUTES['data'][0]];
        yield 'Providers/ProviderItem' => [ProviderItem::class, ProvidersListFixture::ATTRIBUTES['data'][0]];
        yield 'Rerank/RerankResult' => [RerankResult::class, RerankFixture::ATTRIBUTES['results'][0]];
        yield 'Responses/CreateResponseUsage' => [CreateResponseUsage::class, ResponsesCreateFixture::ATTRIBUTES['usage']];
    }

    /**
     * @param  class-string  $class
     * @param  array<string, mixed>  $payload
     */
    #[DataProvider('entityProvider')]
    public function testUnknownFieldsAreExposedAsExtras(string $class, array $payload): void
    {
        $payload['a_field_the_sdk_does_not_know'] = ['nested' => 42];

        $object = $class::from($payload);

        $this->assertSame(
            ['nested' => 42],
            $object->extras['a_field_the_sdk_does_not_know'] ?? null,
            $class.' dropped an unknown field instead of exposing it via $extras',
        );
    }

    /**
     * @param  class-string  $class
     * @param  array<string, mixed>  $payload
     */
    #[DataProvider('entityProvider')]
    public function testUnknownFieldsSurviveToArray(string $class, array $payload): void
    {
        $payload['a_field_the_sdk_does_not_know'] = ['nested' => 42];

        $array = $class::from($payload)->toArray();

        $this->assertSame(
            ['nested' => 42],
            $array['a_field_the_sdk_does_not_know'] ?? null,
            $class.'::toArray() dropped an unknown field',
        );
    }

    /**
     * @param  class-string  $class
     * @param  array<string, mixed>  $payload
     */
    #[DataProvider('entityProvider')]
    public function testKnownFieldsAreNotDuplicatedIntoExtras(string $class, array $payload): void
    {
        $object = $class::from($payload);

        $this->assertSame(
            [],
            $object->extras,
            $class.' leaked known fields into $extras',
        );
    }
}
