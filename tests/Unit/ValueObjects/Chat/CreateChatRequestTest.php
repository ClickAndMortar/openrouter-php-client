<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\ValueObjects\Chat;

use OpenRouter\Enums\Responses\OutputModality;
use OpenRouter\Enums\Responses\ServiceTier;
use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\ValueObjects\Chat\Config\ChatReasoningConfig;
use OpenRouter\ValueObjects\Chat\Config\ChatStreamOptions;
use OpenRouter\ValueObjects\Chat\Config\ChatToolChoice;
use OpenRouter\ValueObjects\Chat\Config\JsonObjectResponseFormat;
use OpenRouter\ValueObjects\Chat\Content\ChatCacheControl;
use OpenRouter\ValueObjects\Chat\Content\ChatTextPart;
use OpenRouter\ValueObjects\Chat\CreateChatRequest;
use OpenRouter\ValueObjects\Chat\Messages\AssistantMessage;
use OpenRouter\ValueObjects\Chat\Messages\SystemMessage;
use OpenRouter\ValueObjects\Chat\Messages\UserMessage;
use OpenRouter\ValueObjects\Chat\Tools\ChatFunctionTool;
use PHPUnit\Framework\TestCase;

final class CreateChatRequestTest extends TestCase
{
    public function testRejectsEmptyMessages(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CreateChatRequest(messages: []);
    }

    public function testRejectsEmptyModelString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CreateChatRequest(messages: [new UserMessage('hi')], model: '');
    }

    public function testRejectsTooManyStopSequences(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CreateChatRequest(
            messages: [new UserMessage('hi')],
            stop: ['a', 'b', 'c', 'd', 'e'],
        );
    }

    public function testRejectsTopLogprobsOutOfRange(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CreateChatRequest(messages: [new UserMessage('hi')], topLogprobs: 21);
    }

    public function testRejectsTooManyMetadataEntries(): void
    {
        $metadata = [];
        for ($i = 0; $i < 17; $i++) {
            $metadata['k'.$i] = 'v';
        }

        $this->expectException(InvalidArgumentException::class);
        new CreateChatRequest(messages: [new UserMessage('hi')], metadata: $metadata);
    }

    public function testToArrayOmitsUnsetOptionalFields(): void
    {
        $req = new CreateChatRequest(
            messages: [new UserMessage('hi')],
            model: 'openai/gpt-4o',
        );

        $array = $req->toArray();

        $this->assertSame(['messages', 'model'], array_keys($array));
        $this->assertSame('openai/gpt-4o', $array['model']);
    }

    public function testToArraySerializesTypedNestedObjects(): void
    {
        $req = new CreateChatRequest(
            messages: [
                new SystemMessage([new ChatTextPart('You are helpful.')]),
                new UserMessage('hi'),
                new AssistantMessage(content: 'hello'),
            ],
            model: 'openai/gpt-4o',
            stream: false,
            streamOptions: new ChatStreamOptions(includeUsage: true),
            temperature: 0.7,
            topP: 0.9,
            topLogprobs: 5,
            logprobs: true,
            maxCompletionTokens: 256,
            frequencyPenalty: 0.1,
            presencePenalty: -0.1,
            seed: 42,
            stop: ['END'],
            user: 'user-123',
            tools: [new ChatFunctionTool(name: 'get_weather', parameters: ['type' => 'object'])],
            toolChoice: ChatToolChoice::auto(),
            parallelToolCalls: true,
            responseFormat: new JsonObjectResponseFormat(),
            reasoning: new ChatReasoningConfig(effort: 'medium'),
            modalities: [OutputModality::Text],
            metadata: ['session_id' => 'abc'],
            serviceTier: ServiceTier::Default_,
            sessionId: 'sess-1',
            cacheControl: new ChatCacheControl(),
            extras: ['x_custom' => 'pass-through'],
        );

        $array = $req->toArray();

        $this->assertSame('openai/gpt-4o', $array['model']);
        $this->assertCount(3, $array['messages']);
        $this->assertSame('system', $array['messages'][0]['role']);
        $this->assertSame('text', $array['messages'][0]['content'][0]['type']);
        $this->assertSame('You are helpful.', $array['messages'][0]['content'][0]['text']);
        $this->assertSame(0.7, $array['temperature']);
        $this->assertSame(['include_usage' => true], $array['stream_options']);
        $this->assertSame('function', $array['tools'][0]['type']);
        $this->assertSame('get_weather', $array['tools'][0]['function']['name']);
        $this->assertSame('auto', $array['tool_choice']);
        $this->assertSame(['type' => 'json_object'], $array['response_format']);
        $this->assertSame(['effort' => 'medium'], $array['reasoning']);
        $this->assertSame(['text'], $array['modalities']);
        $this->assertSame('default', $array['service_tier']);
        $this->assertSame(['END'], $array['stop']);
        $this->assertSame(['type' => 'ephemeral'], $array['cache_control']);
        $this->assertSame('pass-through', $array['x_custom']);
        $this->assertFalse($array['stream'], 'stream:false is non-null so should be emitted');
    }

    public function testToArrayAcceptsPlainArrayMessages(): void
    {
        $req = new CreateChatRequest(
            messages: [['role' => 'user', 'content' => 'hi']],
            model: 'm',
        );

        $array = $req->toArray();

        $this->assertSame([['role' => 'user', 'content' => 'hi']], $array['messages']);
    }

    public function testToolChoiceFunctionVariantSerializesAsObject(): void
    {
        $req = new CreateChatRequest(
            messages: [new UserMessage('hi')],
            toolChoice: ChatToolChoice::function('get_weather'),
        );

        $array = $req->toArray();
        $this->assertSame(['type' => 'function', 'function' => ['name' => 'get_weather']], $array['tool_choice']);
    }
}
