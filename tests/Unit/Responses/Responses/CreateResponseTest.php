<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Responses\Responses;

use OpenRouter\Responses\Meta\MetaInformation;
use OpenRouter\Responses\Responses\CreateResponse;
use OpenRouter\Responses\Responses\CreateResponseOutputFunctionCall;
use OpenRouter\Responses\Responses\CreateResponseOutputMessage;
use OpenRouter\Responses\Responses\CreateResponseOutputReasoning;
use OpenRouter\Responses\Responses\CreateResponseOutputUnknown;
use OpenRouter\Tests\Fixtures\ResponsesCreateFixture;
use OpenRouter\Tests\Fixtures\ResponsesCreateWithRichOutputFixture;
use PHPUnit\Framework\TestCase;

final class CreateResponseTest extends TestCase
{
    public function testFromParsesFixtureAttributes(): void
    {
        $response = CreateResponse::from(ResponsesCreateFixture::ATTRIBUTES, MetaInformation::from([]));

        $this->assertSame('resp-abc123', $response->id);
        $this->assertSame('response', $response->object);
        $this->assertSame('openai/gpt-4o', $response->model);
        $this->assertSame('completed', $response->status);
    }

    public function testToArrayIncludesRawOutput(): void
    {
        $response = CreateResponse::from(ResponsesCreateFixture::ATTRIBUTES, MetaInformation::from([]));
        $data = $response->toArray();

        $this->assertSame('resp-abc123', $data['id']);
        $this->assertSame(ResponsesCreateFixture::ATTRIBUTES['output'], $data['output']);
        $this->assertArrayHasKey('usage', $data);
    }

    public function testArrayAccessReadsUnderlyingToArray(): void
    {
        $response = CreateResponse::from(ResponsesCreateFixture::ATTRIBUTES, MetaInformation::from([]));

        $this->assertSame('resp-abc123', $response['id']);
        $this->assertSame('completed', $response['status']);
        $this->assertTrue(isset($response['model']));
    }

    public function testArrayAccessSetterThrows(): void
    {
        $response = CreateResponse::from(ResponsesCreateFixture::ATTRIBUTES, MetaInformation::from([]));

        $this->expectException(\BadMethodCallException::class);
        $response['id'] = 'nope';
    }

    public function testMetaIsAccessible(): void
    {
        $response = CreateResponse::from(
            ResponsesCreateFixture::ATTRIBUTES,
            MetaInformation::from(['x-request-id' => ['req-abc']]),
        );

        $this->assertSame('req-abc', $response->meta()->requestId);
    }

    public function testFromHydratesPolymorphicOutputItems(): void
    {
        $attributes = ResponsesCreateFixture::ATTRIBUTES;
        $attributes['output'][] = [
            'id' => 'fc-xyz',
            'type' => 'function_call',
            'call_id' => 'call-xyz',
            'name' => 'get_weather',
            'arguments' => '{"location":"Paris"}',
            'status' => 'completed',
        ];

        $response = CreateResponse::from($attributes, MetaInformation::from([]));

        $this->assertCount(2, $response->output, 'Output now exposes all typed items.');
        $this->assertCount(2, $response->rawOutput, 'Raw output retains all items.');

        $this->assertInstanceOf(CreateResponseOutputMessage::class, $response->output[0]);
        $this->assertSame('msg-abc123', $response->output[0]->id());

        $this->assertInstanceOf(CreateResponseOutputFunctionCall::class, $response->output[1]);
        $this->assertSame('fc-xyz', $response->output[1]->id());
        $this->assertSame('call-xyz', $response->output[1]->callId);
        $this->assertSame('get_weather', $response->output[1]->name);
    }

    public function testTextPrefersOutputTextShortCircuit(): void
    {
        $attributes = ResponsesCreateFixture::ATTRIBUTES;
        $attributes['output_text'] = 'Precomputed text';

        $response = CreateResponse::from($attributes, MetaInformation::from([]));

        $this->assertSame('Precomputed text', $response->text());
    }

    public function testTextJoinsOutputTextPartsWhenNoShortCircuit(): void
    {
        $attributes = ResponsesCreateFixture::ATTRIBUTES;
        $attributes['output'][0]['content'] = [
            ['type' => 'output_text', 'text' => 'Hello '],
            ['type' => 'output_text', 'text' => 'world'],
        ];

        $response = CreateResponse::from($attributes, MetaInformation::from([]));

        $this->assertSame('Hello world', $response->text());
    }

    public function testTextReturnsNullForToolCallOnlyOutput(): void
    {
        $attributes = ResponsesCreateFixture::ATTRIBUTES;
        $attributes['output'] = [[
            'id' => 'fc-1',
            'type' => 'function_call',
            'call_id' => 'call-1',
            'name' => 'get_weather',
            'arguments' => '{"location":"Paris"}',
        ]];

        $response = CreateResponse::from($attributes, MetaInformation::from([]));

        $this->assertNull($response->text());
        $calls = $response->toolCalls();
        $this->assertCount(1, $calls);
        $this->assertSame('get_weather', $calls[0]->name);
        $this->assertSame(['location' => 'Paris'], $calls[0]->decodedArguments());
        $this->assertSame($calls[0], $response->functionCall('get_weather'));
        $this->assertNull($response->functionCall('missing'));
    }

    public function testMessagesAndReasoningFiltersOutputByType(): void
    {
        $response = CreateResponse::from(
            ResponsesCreateWithRichOutputFixture::ATTRIBUTES,
            MetaInformation::from([]),
        );

        $this->assertGreaterThanOrEqual(1, count($response->messages()));
        $this->assertContainsOnlyInstancesOf(CreateResponseOutputMessage::class, $response->messages());
        $this->assertContainsOnlyInstancesOf(CreateResponseOutputReasoning::class, $response->reasoning());
    }

    public function testFromHydratesRichOutputFixture(): void
    {
        $response = CreateResponse::from(
            ResponsesCreateWithRichOutputFixture::ATTRIBUTES,
            MetaInformation::from([]),
        );

        $this->assertCount(9, $response->output);

        $this->assertInstanceOf(CreateResponseOutputMessage::class, $response->output[0]);
        $this->assertInstanceOf(CreateResponseOutputReasoning::class, $response->output[1]);
        $this->assertInstanceOf(CreateResponseOutputFunctionCall::class, $response->output[2]);

        $this->assertSame('reasoning-1', $response->output[1]->id());
        $this->assertSame('sig-abc', $response->output[1]->signature);

        $this->assertInstanceOf(CreateResponseOutputUnknown::class, $response->output[8]);
        $this->assertSame('some_future_type_not_in_v1', $response->output[8]->type());
    }
}
