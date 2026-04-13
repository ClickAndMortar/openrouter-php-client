<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Responses\Responses;

use OpenRouter\Responses\Responses\CreateResponseOutputDatetime;
use OpenRouter\Responses\Responses\CreateResponseOutputFileSearchCall;
use OpenRouter\Responses\Responses\CreateResponseOutputFunctionCall;
use OpenRouter\Responses\Responses\CreateResponseOutputImageGenerationCall;
use OpenRouter\Responses\Responses\CreateResponseOutputItemFactory;
use OpenRouter\Responses\Responses\CreateResponseOutputMessage;
use OpenRouter\Responses\Responses\CreateResponseOutputReasoning;
use OpenRouter\Responses\Responses\CreateResponseOutputUnknown;
use OpenRouter\Responses\Responses\CreateResponseOutputWebSearch;
use OpenRouter\Responses\Responses\CreateResponseOutputWebSearchCall;
use PHPUnit\Framework\TestCase;

final class CreateResponseOutputItemFactoryTest extends TestCase
{
    public function testDispatchesMessage(): void
    {
        $item = CreateResponseOutputItemFactory::from([
            'id' => 'msg-1',
            'type' => 'message',
            'role' => 'assistant',
            'status' => 'completed',
            'content' => [['type' => 'output_text', 'text' => 'hi', 'annotations' => []]],
        ]);

        $this->assertInstanceOf(CreateResponseOutputMessage::class, $item);
        $this->assertSame('msg-1', $item->id());
        $this->assertSame('message', $item->type());
    }

    public function testDispatchesReasoning(): void
    {
        $item = CreateResponseOutputItemFactory::from([
            'id' => 'r-1',
            'type' => 'reasoning',
            'status' => 'completed',
            'summary' => [['type' => 'summary_text', 'text' => 'plan']],
            'signature' => 'sig',
        ]);

        $this->assertInstanceOf(CreateResponseOutputReasoning::class, $item);
        $this->assertSame('r-1', $item->id);
        $this->assertSame('sig', $item->signature);
    }

    public function testDispatchesFunctionCall(): void
    {
        $item = CreateResponseOutputItemFactory::from([
            'id' => 'fc-1',
            'type' => 'function_call',
            'call_id' => 'call-abc',
            'name' => 'get_weather',
            'arguments' => '{"city":"Paris"}',
            'status' => 'completed',
        ]);

        $this->assertInstanceOf(CreateResponseOutputFunctionCall::class, $item);
        $this->assertSame('call-abc', $item->callId);
        $this->assertSame('get_weather', $item->name);
        $this->assertSame('{"city":"Paris"}', $item->arguments);
    }

    public function testDispatchesWebSearchCall(): void
    {
        $item = CreateResponseOutputItemFactory::from([
            'id' => 'ws-1',
            'type' => 'web_search_call',
            'status' => 'completed',
            'action' => ['type' => 'search', 'query' => 'php'],
        ]);

        $this->assertInstanceOf(CreateResponseOutputWebSearchCall::class, $item);
        $this->assertSame('completed', $item->status);
        $this->assertSame(['type' => 'search', 'query' => 'php'], $item->action);
    }

    public function testDispatchesFileSearchCall(): void
    {
        $item = CreateResponseOutputItemFactory::from([
            'id' => 'fs-1',
            'type' => 'file_search_call',
            'queries' => ['invoice 2024'],
            'status' => 'completed',
        ]);

        $this->assertInstanceOf(CreateResponseOutputFileSearchCall::class, $item);
        $this->assertSame(['invoice 2024'], $item->queries);
    }

    public function testDispatchesImageGenerationCall(): void
    {
        $item = CreateResponseOutputItemFactory::from([
            'id' => 'img-1',
            'type' => 'image_generation_call',
            'status' => 'completed',
            'result' => 'AAAA',
        ]);

        $this->assertInstanceOf(CreateResponseOutputImageGenerationCall::class, $item);
        $this->assertSame('AAAA', $item->result);
    }

    public function testDispatchesDatetime(): void
    {
        $item = CreateResponseOutputItemFactory::from([
            'id' => 'dt-1',
            'type' => 'openrouter:datetime',
            'status' => 'completed',
            'datetime' => '2026-04-10T12:34:56Z',
            'timezone' => 'Europe/Paris',
        ]);

        $this->assertInstanceOf(CreateResponseOutputDatetime::class, $item);
        $this->assertSame('Europe/Paris', $item->timezone);
    }

    public function testDispatchesOpenRouterWebSearch(): void
    {
        $item = CreateResponseOutputItemFactory::from([
            'id' => 'orws-1',
            'type' => 'openrouter:web_search',
            'status' => 'completed',
        ]);

        $this->assertInstanceOf(CreateResponseOutputWebSearch::class, $item);
        $this->assertSame('completed', $item->status);
    }

    public function testDispatchesUnknownFallback(): void
    {
        $item = CreateResponseOutputItemFactory::from([
            'id' => 'future-1',
            'type' => 'some_future_type_not_in_v1',
            'future_field' => 'payload',
        ]);

        $this->assertInstanceOf(CreateResponseOutputUnknown::class, $item);
        $this->assertSame('some_future_type_not_in_v1', $item->type());
        $this->assertSame('future-1', $item->id());
        $this->assertSame(
            ['id' => 'future-1', 'type' => 'some_future_type_not_in_v1', 'future_field' => 'payload'],
            $item->toArray(),
        );
    }

    public function testDispatchesUnknownWhenTypeMissing(): void
    {
        $item = CreateResponseOutputItemFactory::from(['id' => 'x']);

        $this->assertInstanceOf(CreateResponseOutputUnknown::class, $item);
        $this->assertSame('unknown', $item->type());
    }
}
