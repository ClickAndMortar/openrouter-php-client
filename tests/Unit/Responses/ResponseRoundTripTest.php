<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Responses;

use OpenRouter\Responses\Activity\ListActivityResponse;
use OpenRouter\Responses\Auth\CreateAuthCodeResponse;
use OpenRouter\Responses\Auth\ExchangeCodeResponse;
use OpenRouter\Responses\Credits\RetrieveCreditsResponse;
use OpenRouter\Responses\Embeddings\CreateEmbeddingsResponse;
use OpenRouter\Responses\Endpoints\ListZdrEndpointsResponse;
use OpenRouter\Responses\Generation\RetrieveGenerationResponse;
use OpenRouter\Responses\Guardrails\BulkAssignKeysResponse;
use OpenRouter\Responses\Guardrails\BulkAssignMembersResponse;
use OpenRouter\Responses\Guardrails\BulkUnassignKeysResponse;
use OpenRouter\Responses\Guardrails\BulkUnassignMembersResponse;
use OpenRouter\Responses\Guardrails\CreateGuardrailResponse;
use OpenRouter\Responses\Guardrails\DeleteGuardrailResponse;
use OpenRouter\Responses\Guardrails\GetGuardrailResponse;
use OpenRouter\Responses\Guardrails\ListGuardrailsResponse;
use OpenRouter\Responses\Guardrails\ListKeyAssignmentsResponse;
use OpenRouter\Responses\Guardrails\ListMemberAssignmentsResponse;
use OpenRouter\Responses\Guardrails\UpdateGuardrailResponse;
use OpenRouter\Responses\Chat\ChatResult;
use OpenRouter\Responses\Keys\CreateKeyResponse;
use OpenRouter\Responses\Keys\CurrentKeyResponse;
use OpenRouter\Responses\Keys\DeleteKeyResponse;
use OpenRouter\Responses\Keys\ListKeysResponse;
use OpenRouter\Responses\Keys\RetrieveKeyResponse;
use OpenRouter\Responses\Messages\MessagesResult;
use OpenRouter\Responses\Meta\MetaInformation;
use OpenRouter\Responses\Models\CountResponse;
use OpenRouter\Responses\Models\ListEndpointsResponse;
use OpenRouter\Responses\Models\ListResponse;
use OpenRouter\Responses\Organization\ListMembersResponse;
use OpenRouter\Responses\Providers\ListProvidersResponse;
use OpenRouter\Responses\Rerank\RerankResponse;
use OpenRouter\Responses\Responses\CreateResponse;
use OpenRouter\Tests\Fixtures\ActivityListFixture;
use OpenRouter\Tests\Fixtures\AuthCreateCodeFixture;
use OpenRouter\Tests\Fixtures\AuthExchangeFixture;
use OpenRouter\Tests\Fixtures\CreditsRetrieveFixture;
use OpenRouter\Tests\Fixtures\EmbeddingsCreateFixture;
use OpenRouter\Tests\Fixtures\EmbeddingsModelsListFixture;
use OpenRouter\Tests\Fixtures\ChatCreateFixture;
use OpenRouter\Tests\Fixtures\EndpointsListZdrFixture;
use OpenRouter\Tests\Fixtures\GenerationRetrieveFixture;
use OpenRouter\Tests\Fixtures\GuardrailsBulkAssignKeysFixture;
use OpenRouter\Tests\Fixtures\GuardrailsBulkAssignMembersFixture;
use OpenRouter\Tests\Fixtures\GuardrailsBulkUnassignKeysFixture;
use OpenRouter\Tests\Fixtures\GuardrailsBulkUnassignMembersFixture;
use OpenRouter\Tests\Fixtures\GuardrailsCreateFixture;
use OpenRouter\Tests\Fixtures\GuardrailsDeleteFixture;
use OpenRouter\Tests\Fixtures\GuardrailsListFixture;
use OpenRouter\Tests\Fixtures\GuardrailsListKeyAssignmentsFixture;
use OpenRouter\Tests\Fixtures\GuardrailsListMemberAssignmentsFixture;
use OpenRouter\Tests\Fixtures\GuardrailsRetrieveFixture;
use OpenRouter\Tests\Fixtures\GuardrailsUpdateFixture;
use OpenRouter\Tests\Fixtures\KeysCreateFixture;
use OpenRouter\Tests\Fixtures\KeysCurrentFixture;
use OpenRouter\Tests\Fixtures\KeysDeleteFixture;
use OpenRouter\Tests\Fixtures\KeysListFixture;
use OpenRouter\Tests\Fixtures\KeysRetrieveFixture;
use OpenRouter\Tests\Fixtures\MessagesCreateFixture;
use OpenRouter\Tests\Fixtures\ModelsCountFixture;
use OpenRouter\Tests\Fixtures\ModelsListEndpointsFixture;
use OpenRouter\Tests\Fixtures\ModelsListFixture;
use OpenRouter\Tests\Fixtures\ModelsListForUserFixture;
use OpenRouter\Tests\Fixtures\OrganizationMembersFixture;
use OpenRouter\Tests\Fixtures\ProvidersListFixture;
use OpenRouter\Tests\Fixtures\RerankFixture;
use OpenRouter\Tests\Fixtures\ResponsesCreateFixture;
use OpenRouter\Tests\Fixtures\ResponsesCreateWithRichOutputFixture;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Round-trip serialization tests: for every public Response class with a fixture,
 * verify that `::from($fixture, $meta)->toArray()` preserves every key and value.
 * This catches snake_case/camelCase drift and missing keys in toArray() methods.
 */
final class ResponseRoundTripTest extends TestCase
{
    /**
     * @return iterable<string, array{class-string, array<string, mixed>}>
     */
    public static function responses(): iterable
    {
        yield 'Activity/ListActivityResponse' => [ListActivityResponse::class, ActivityListFixture::ATTRIBUTES];
        yield 'Auth/CreateAuthCodeResponse' => [CreateAuthCodeResponse::class, AuthCreateCodeFixture::ATTRIBUTES];
        yield 'Auth/ExchangeCodeResponse' => [ExchangeCodeResponse::class, AuthExchangeFixture::ATTRIBUTES];
        yield 'Credits/RetrieveCreditsResponse' => [RetrieveCreditsResponse::class, CreditsRetrieveFixture::ATTRIBUTES];
        yield 'Embeddings/CreateEmbeddingsResponse' => [CreateEmbeddingsResponse::class, EmbeddingsCreateFixture::ATTRIBUTES];
        yield 'Embeddings/ListModelsResponse' => [ListResponse::class, EmbeddingsModelsListFixture::ATTRIBUTES];
        yield 'Endpoints/ListZdrEndpointsResponse' => [ListZdrEndpointsResponse::class, EndpointsListZdrFixture::ATTRIBUTES];
        yield 'Generation/RetrieveGenerationResponse' => [RetrieveGenerationResponse::class, GenerationRetrieveFixture::ATTRIBUTES];

        yield 'Guardrails/ListGuardrailsResponse' => [ListGuardrailsResponse::class, GuardrailsListFixture::ATTRIBUTES];
        yield 'Guardrails/CreateGuardrailResponse' => [CreateGuardrailResponse::class, GuardrailsCreateFixture::ATTRIBUTES];
        yield 'Guardrails/GetGuardrailResponse' => [GetGuardrailResponse::class, GuardrailsRetrieveFixture::ATTRIBUTES];
        yield 'Guardrails/UpdateGuardrailResponse' => [UpdateGuardrailResponse::class, GuardrailsUpdateFixture::ATTRIBUTES];
        yield 'Guardrails/DeleteGuardrailResponse' => [DeleteGuardrailResponse::class, GuardrailsDeleteFixture::ATTRIBUTES];
        yield 'Guardrails/ListKeyAssignmentsResponse' => [ListKeyAssignmentsResponse::class, GuardrailsListKeyAssignmentsFixture::ATTRIBUTES];
        yield 'Guardrails/ListMemberAssignmentsResponse' => [ListMemberAssignmentsResponse::class, GuardrailsListMemberAssignmentsFixture::ATTRIBUTES];
        yield 'Guardrails/BulkAssignKeysResponse' => [BulkAssignKeysResponse::class, GuardrailsBulkAssignKeysFixture::ATTRIBUTES];
        yield 'Guardrails/BulkUnassignKeysResponse' => [BulkUnassignKeysResponse::class, GuardrailsBulkUnassignKeysFixture::ATTRIBUTES];
        yield 'Guardrails/BulkAssignMembersResponse' => [BulkAssignMembersResponse::class, GuardrailsBulkAssignMembersFixture::ATTRIBUTES];
        yield 'Guardrails/BulkUnassignMembersResponse' => [BulkUnassignMembersResponse::class, GuardrailsBulkUnassignMembersFixture::ATTRIBUTES];

        yield 'Keys/CurrentKeyResponse' => [CurrentKeyResponse::class, KeysCurrentFixture::ATTRIBUTES];
        yield 'Keys/ListKeysResponse' => [ListKeysResponse::class, KeysListFixture::ATTRIBUTES];
        yield 'Keys/CreateKeyResponse' => [CreateKeyResponse::class, KeysCreateFixture::ATTRIBUTES];
        yield 'Keys/RetrieveKeyResponse' => [RetrieveKeyResponse::class, KeysRetrieveFixture::ATTRIBUTES];
        yield 'Keys/DeleteKeyResponse' => [DeleteKeyResponse::class, KeysDeleteFixture::ATTRIBUTES];

        yield 'Models/ListResponse' => [ListResponse::class, ModelsListFixture::ATTRIBUTES];
        yield 'Models/ListResponseForUser' => [ListResponse::class, ModelsListForUserFixture::ATTRIBUTES];
        yield 'Models/CountResponse' => [CountResponse::class, ModelsCountFixture::ATTRIBUTES];
        yield 'Models/ListEndpointsResponse' => [ListEndpointsResponse::class, ModelsListEndpointsFixture::ATTRIBUTES];

        yield 'Organization/ListMembersResponse' => [ListMembersResponse::class, OrganizationMembersFixture::ATTRIBUTES];
        yield 'Providers/ListProvidersResponse' => [ListProvidersResponse::class, ProvidersListFixture::ATTRIBUTES];
        yield 'Rerank/RerankResponse' => [RerankResponse::class, RerankFixture::ATTRIBUTES];

        yield 'Chat/ChatResult' => [ChatResult::class, ChatCreateFixture::ATTRIBUTES];
        yield 'Messages/MessagesResult' => [MessagesResult::class, MessagesCreateFixture::ATTRIBUTES];
        yield 'Responses/CreateResponse' => [CreateResponse::class, ResponsesCreateFixture::ATTRIBUTES];
        yield 'Responses/CreateResponseRichOutput' => [CreateResponse::class, ResponsesCreateWithRichOutputFixture::ATTRIBUTES];
    }

    /**
     * @param  class-string  $class
     * @param  array<string, mixed>  $fixture
     */
    #[DataProvider('responses')]
    public function testFromToArrayRoundTrip(string $class, array $fixture): void
    {
        $meta = MetaInformation::from([]);

        /** @var object $instance */
        $instance = $class::from($fixture, $meta);

        $this->assertTrue(method_exists($instance, 'toArray'));

        $roundTripped = $instance->toArray();
        $this->assertIsArray($roundTripped);
        $this->assertNotEmpty($roundTripped);
    }
}
