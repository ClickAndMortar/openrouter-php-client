<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Responses\Models;

use OpenRouter\Responses\Meta\MetaInformation;
use OpenRouter\Responses\Models\ListResponse;
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

    public function testArrayAccessWorks(): void
    {
        $response = ListResponse::from(ModelsListForUserFixture::ATTRIBUTES, MetaInformation::from([]));

        $this->assertIsArray($response['data']);
        $this->assertCount(1, $response['data']);
    }
}
