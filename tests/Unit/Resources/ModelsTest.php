<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Resources;

use OpenRouter\Factory;
use OpenRouter\Responses\Models\ListResponse;
use OpenRouter\Tests\Doubles\RecordingHttpClient;
use OpenRouter\Tests\Fixtures\ModelsListForUserFixture;
use PHPUnit\Framework\TestCase;

final class ModelsTest extends TestCase
{
    public function testListForUserHitsCorrectEndpointAsGet(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ModelsListForUserFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $client->models()->listForUser();

        $request = $http->lastRequest();
        $this->assertSame('GET', $request->getMethod());
        $this->assertStringEndsWith('/models/user', (string) $request->getUri());
        $this->assertSame('', (string) $request->getBody());
    }

    public function testListForUserReturnsTypedListResponse(): void
    {
        $http = new RecordingHttpClient();
        $http->enqueueJson(ModelsListForUserFixture::ATTRIBUTES);

        $client = (new Factory())->withApiKey('sk-or-test')->withHttpClient($http)->make();

        $response = $client->models()->listForUser();

        $this->assertInstanceOf(ListResponse::class, $response);
        $this->assertCount(1, $response->data);

        $model = $response->data[0];
        $this->assertSame('openai/gpt-4', $model->id);
        $this->assertSame('openai/gpt-4', $model->canonicalSlug);
        $this->assertSame('GPT-4', $model->name);
        $this->assertSame(1692901234, $model->created);
        $this->assertSame(8192, $model->contextLength);

        $this->assertSame(['text'], $model->architecture->inputModalities);
        $this->assertSame(['text'], $model->architecture->outputModalities);
        $this->assertSame('chatml', $model->architecture->instructType);
        $this->assertSame('text->text', $model->architecture->modality);
        $this->assertSame('GPT', $model->architecture->tokenizer);

        $this->assertSame('0.00003', $model->pricing->prompt);
        $this->assertSame('0.00006', $model->pricing->completion);
        $this->assertSame('0', $model->pricing->image);

        $this->assertTrue($model->topProvider->isModerated);
        $this->assertSame(8192, $model->topProvider->contextLength);
        $this->assertSame(4096, $model->topProvider->maxCompletionTokens);

        $this->assertSame(['temperature', 'top_p', 'max_tokens'], $model->supportedParameters);
    }
}
