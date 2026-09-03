<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Resources;

use OpenRouter\Factory;
use OpenRouter\Responses\Presets\ListPresetsResponse;
use OpenRouter\Responses\Presets\ListPresetVersionsResponse;
use OpenRouter\Responses\Presets\PresetResponse;
use OpenRouter\Responses\Presets\PresetVersionResponse;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use OpenRouter\Tests\Fixtures\PresetRetrieveFixture;
use OpenRouter\Tests\Fixtures\PresetsListFixture;
use OpenRouter\Tests\Fixtures\PresetVersionsFixture;
use OpenRouter\ValueObjects\Chat\CreateChatRequest;
use OpenRouter\ValueObjects\Chat\Messages\UserMessage;
use PHPUnit\Framework\TestCase;

final class PresetsTest extends TestCase
{
    private function client(RecordingHttpClient $http): \OpenRouter\Client
    {
        return (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();
    }

    public function testListPaginatesAndReturnsTypedPresets(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(PresetsListFixture::ATTRIBUTES);

        $response = $this->client($http)->presets()->list(limit: 5, offset: 10);

        $query = [];
        parse_str($http->lastRequest()->getUri()->getQuery(), $query);
        $this->assertSame('5', $query['limit']);
        $this->assertSame('10', $query['offset']);

        $this->assertInstanceOf(ListPresetsResponse::class, $response);
        $this->assertSame(1, $response->totalCount);
        $this->assertSame('support-agent', $response->data[0]->slug);
        $this->assertSame('active', $response->data[0]->status);
        $this->assertSame('pv_01HQ8Z3K4M5N6P7Q8R9S', $response->data[0]->designatedVersionId);
    }

    public function testRetrieveUsesTheSlug(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(PresetRetrieveFixture::ATTRIBUTES);

        $response = $this->client($http)->presets()->retrieve('support-agent');

        $this->assertSame('GET', $http->lastRequest()->getMethod());
        $this->assertStringEndsWith('/presets/support-agent', (string) $http->lastRequest()->getUri());
        $this->assertInstanceOf(PresetResponse::class, $response);
        $this->assertSame('Support agent', $response->data->name);
    }

    public function testListVersionsReturnsTypedVersions(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(PresetVersionsFixture::ATTRIBUTES);

        $response = $this->client($http)->presets()->listVersions('support-agent', limit: 3);

        $this->assertStringContainsString('/presets/support-agent/versions', (string) $http->lastRequest()->getUri());
        $this->assertInstanceOf(ListPresetVersionsResponse::class, $response);
        $this->assertSame(3, $response->data[0]->version);
        $this->assertSame('You are a support agent.', $response->data[0]->systemPrompt);
        $this->assertSame(
            ['model' => 'openai/gpt-4o', 'temperature' => 0.2],
            $response->data[0]->config,
        );
    }

    public function testRetrieveVersionAcceptsAnIntegerVersion(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['data' => PresetVersionsFixture::ATTRIBUTES['data'][0]]);

        $response = $this->client($http)->presets()->retrieveVersion('support-agent', 3);

        $this->assertStringEndsWith('/presets/support-agent/versions/3', (string) $http->lastRequest()->getUri());
        $this->assertInstanceOf(PresetVersionResponse::class, $response);
        $this->assertSame(3, $response->data?->version);
    }

    public function testRetrieveVersionToleratesANullVersionPayload(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['data' => null]);

        $response = $this->client($http)->presets()->retrieveVersion('support-agent', 99);

        $this->assertNull($response->data);
    }

    public function testCreateFromChatPostsTheChatRequestBody(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(PresetRetrieveFixture::ATTRIBUTES);

        $response = $this->client($http)->presets()->createFromChat('support-agent', new CreateChatRequest(
            model: 'openai/gpt-4o',
            messages: [new UserMessage('hi')],
            temperature: 0.2,
        ));

        $request = $http->lastRequest();
        $this->assertSame('POST', $request->getMethod());
        $this->assertStringEndsWith('/presets/support-agent/chat/completions', (string) $request->getUri());

        $body = json_decode((string) $request->getBody(), true);
        $this->assertSame('openai/gpt-4o', $body['model']);
        $this->assertSame(0.2, $body['temperature']);

        $this->assertInstanceOf(PresetResponse::class, $response);
        $this->assertSame('support-agent', $response->data->slug);
    }

    public function testCreateFromMessagesAndResponsesHitTheirEndpoints(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(PresetRetrieveFixture::ATTRIBUTES);
        $http->enqueueJson(PresetRetrieveFixture::ATTRIBUTES);

        $presets = $this->client($http)->presets();

        $presets->createFromMessages('support-agent', ['model' => 'anthropic/claude-sonnet-4', 'messages' => []]);
        $this->assertStringEndsWith('/presets/support-agent/messages', (string) $http->lastRequest()->getUri());

        $presets->createFromResponses('support-agent', ['model' => 'openai/gpt-4o', 'input' => 'hi']);
        $this->assertStringEndsWith('/presets/support-agent/responses', (string) $http->lastRequest()->getUri());
    }

    public function testPresetKeepsUnknownFieldsInExtras(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(['data' => [...PresetRetrieveFixture::ATTRIBUTES['data'], 'a_new_field' => 'x']]);

        $response = $this->client($http)->presets()->retrieve('support-agent');

        $this->assertSame('x', $response->data->extras['a_new_field']);
    }
}
