<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Responses\Models;

use OpenRouter\Responses\Meta\MetaInformation;
use OpenRouter\Responses\Models\ListResponse;
use OpenRouter\Tests\Fixtures\ModelsListFixture;
use OpenRouter\Tests\Fixtures\ModelsListForUserFixture;
use PHPUnit\Framework\TestCase;

final class ListResponseTest extends TestCase
{
    public function testFromParsesFixture(): void
    {
        $response = ListResponse::from(ModelsListForUserFixture::ATTRIBUTES, MetaInformation::from([]));

        $this->assertCount(1, $response->data);
        $this->assertSame('openai/gpt-4', $response->data[0]->id);
        $this->assertSame('GPT-4', $response->data[0]->name);
    }

    public function testToArrayRoundTripPreservesScalarsAndNested(): void
    {
        $response = ListResponse::from(ModelsListForUserFixture::ATTRIBUTES, MetaInformation::from([]));
        $data = $response->toArray();

        $this->assertSame(ModelsListForUserFixture::ATTRIBUTES['data'][0]['id'], $data['data'][0]['id']);
        $this->assertSame(ModelsListForUserFixture::ATTRIBUTES['data'][0]['name'], $data['data'][0]['name']);
        $this->assertSame(ModelsListForUserFixture::ATTRIBUTES['data'][0]['architecture']['modality'], $data['data'][0]['architecture']['modality']);
        $this->assertSame(ModelsListForUserFixture::ATTRIBUTES['data'][0]['pricing']['prompt'], $data['data'][0]['pricing']['prompt']);
    }

    public function testFromExposesPaginationMetadata(): void
    {
        $response = ListResponse::from(ModelsListFixture::ATTRIBUTES, MetaInformation::from([]));

        $this->assertSame(517, $response->totalCount);
        $this->assertSame('/api/v1/models?offset=500&limit=500', $response->nextPage);
    }

    public function testPaginationMetadataIsNullWhenAbsent(): void
    {
        $response = ListResponse::from(['data' => []], MetaInformation::from([]));

        $this->assertNull($response->totalCount);
        $this->assertNull($response->nextPage);
    }

    public function testNextPageIsNullOnLastPage(): void
    {
        $response = ListResponse::from(
            ['data' => [], 'total_count' => 12, 'links' => ['next' => null]],
            MetaInformation::from([]),
        );

        $this->assertSame(12, $response->totalCount);
        $this->assertNull($response->nextPage);
    }

    public function testToArrayIncludesPaginationMetadata(): void
    {
        $response = ListResponse::from(ModelsListFixture::ATTRIBUTES, MetaInformation::from([]));

        $data = $response->toArray();

        $this->assertSame(517, $data['total_count']);
        $this->assertSame(['next' => '/api/v1/models?offset=500&limit=500'], $data['links']);
    }

    public function testArrayAccessWorks(): void
    {
        $response = ListResponse::from(ModelsListForUserFixture::ATTRIBUTES, MetaInformation::from([]));

        $this->assertIsArray($response['data']);
        $this->assertCount(1, $response['data']);
    }
}
