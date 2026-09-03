<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Resources;

use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\Factory;
use OpenRouter\Responses\Observability\DeleteObservabilityDestinationResponse;
use OpenRouter\Responses\Observability\ListObservabilityDestinationsResponse;
use OpenRouter\Responses\Observability\ObservabilityDestinationResponse;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use OpenRouter\ValueObjects\Observability\CreateObservabilityDestinationRequest;
use OpenRouter\ValueObjects\Observability\UpdateObservabilityDestinationRequest;
use PHPUnit\Framework\TestCase;

final class ObservabilityTest extends TestCase
{
    private const DESTINATION = [
        'id' => 'obs_01HQ8Z3K4M5N6P7Q8R9S',
        'type' => 'langfuse',
        'name' => 'Langfuse prod',
        'enabled' => true,
        'config' => ['host' => 'https://cloud.langfuse.com'],
        'sampling_rate' => 0.5,
        'privacy_mode' => false,
        'broadcast_generation_cost' => true,
        'broadcast_generation_identity' => false,
        'broadcast_generation_request_context' => true,
        'api_key_hashes' => ['abc123'],
        'filter_rules' => null,
        'created_at' => '2026-01-04T10:00:00Z',
        'updated_at' => '2026-02-01T09:30:00Z',
    ];

    private function client(RecordingHttpClient $http): \OpenRouter\Client
    {
        return (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();
    }

    public function testListReturnsTypedDestinations(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['data' => [self::DESTINATION], 'total_count' => 1]);

        $response = $this->client($http)->observability()->list(limit: 10, workspaceId: 'ws_1');

        $query = [];
        parse_str($http->lastRequest()->getUri()->getQuery(), $query);
        $this->assertSame('10', $query['limit']);
        $this->assertSame('ws_1', $query['workspace_id']);

        $this->assertInstanceOf(ListObservabilityDestinationsResponse::class, $response);
        $this->assertSame(1, $response->totalCount);

        $destination = $response->data[0];
        $this->assertSame('langfuse', $destination->type);
        $this->assertSame('Langfuse prod', $destination->name);
        $this->assertTrue($destination->enabled);
        $this->assertSame(['host' => 'https://cloud.langfuse.com'], $destination->config);
        $this->assertSame(0.5, $destination->samplingRate);
        $this->assertTrue($destination->broadcastGenerationCost);
    }

    public function testCreatePostsTypeNameAndConfig(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['data' => self::DESTINATION]);

        $response = $this->client($http)->observability()->create(
            new CreateObservabilityDestinationRequest(
                type: 'langfuse',
                name: 'Langfuse prod',
                config: ['host' => 'https://cloud.langfuse.com'],
                samplingRate: 0.5,
                enabled: true,
            ),
        );

        $request = $http->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/observability/destinations', (string) $request->getUri());

        $body = json_decode((string) $request->getBody(), true);
        $this->assertSame('langfuse', $body['type']);
        $this->assertSame('Langfuse prod', $body['name']);
        $this->assertSame(['host' => 'https://cloud.langfuse.com'], $body['config']);
        $this->assertSame(0.5, $body['sampling_rate']);

        $this->assertInstanceOf(ObservabilityDestinationResponse::class, $response);
        $this->assertSame('langfuse', $response->data->type);
    }

    public function testCreateRejectsAnEmptyType(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CreateObservabilityDestinationRequest(type: '', name: 'x', config: []);
    }

    public function testRetrieveUpdateAndDelete(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['data' => self::DESTINATION]);
        $http->enqueueJson(['data' => self::DESTINATION]);
        $http->enqueueJson(['deleted' => true]);

        $observability = $this->client($http)->observability();

        $observability->retrieve('obs_1');
        $this->assertStringEndsWith('/observability/destinations/obs_1', (string) $http->lastRequest()->getUri());

        $observability->update('obs_1', new UpdateObservabilityDestinationRequest(enabled: false));
        $this->assertSame('PATCH', $http->lastRequest()->getMethod());
        $this->assertSame(['enabled' => false], json_decode((string) $http->lastRequest()->getBody(), true));

        $deleted = $observability->delete('obs_1');
        $this->assertSame('DELETE', $http->lastRequest()->getMethod());
        $this->assertInstanceOf(DeleteObservabilityDestinationResponse::class, $deleted);
        $this->assertTrue($deleted->deleted);
    }

    public function testDestinationKeepsUnknownFieldsInExtras(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['data' => [...self::DESTINATION, 'a_new_field' => 'x']]);

        $response = $this->client($http)->observability()->retrieve('obs_1');

        $this->assertSame('x', $response->data->extras['a_new_field']);
    }
}
