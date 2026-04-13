<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\ValueObjects\Responses;

use OpenRouter\Enums\Responses\OutputModality;
use OpenRouter\Enums\Responses\ReasoningEffort;
use OpenRouter\Enums\Responses\ResponseIncludes;
use OpenRouter\Enums\Responses\ServiceTier;
use OpenRouter\Enums\Responses\Truncation;
use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\ValueObjects\Responses\Config\ProviderPreferences;
use OpenRouter\ValueObjects\Responses\Config\ReasoningConfig;
use OpenRouter\ValueObjects\Responses\Config\StoredPromptTemplate;
use OpenRouter\ValueObjects\Responses\Config\TextExtendedConfig;
use OpenRouter\ValueObjects\Responses\Config\ToolChoice;
use OpenRouter\ValueObjects\Responses\Config\TraceConfig;
use OpenRouter\ValueObjects\Responses\CreateResponseRequest;
use OpenRouter\ValueObjects\Responses\Input\Content\InputAudioPart;
use OpenRouter\ValueObjects\Responses\Input\Content\InputFilePart;
use OpenRouter\ValueObjects\Responses\Input\Content\InputImagePart;
use OpenRouter\ValueObjects\Responses\Input\Content\InputTextPart;
use OpenRouter\ValueObjects\Responses\Input\Content\InputVideoPart;
use OpenRouter\ValueObjects\Responses\Input\InputFunctionCall;
use OpenRouter\ValueObjects\Responses\Input\InputFunctionCallOutput;
use OpenRouter\ValueObjects\Responses\Input\InputMessage;
use OpenRouter\ValueObjects\Responses\Input\InputReasoning;
use OpenRouter\ValueObjects\Responses\Input\OutputMessage;
use OpenRouter\ValueObjects\Responses\Plugins\WebSearchPlugin;
use OpenRouter\ValueObjects\Responses\Tools\FunctionTool;
use PHPUnit\Framework\TestCase;

final class CreateResponseRequestTest extends TestCase
{
    public function testStringInputSerializesDirectly(): void
    {
        $request = new CreateResponseRequest(model: 'openai/gpt-4o', input: 'Tell me a joke');

        $this->assertSame([
            'model' => 'openai/gpt-4o',
            'input' => 'Tell me a joke',
        ], $request->toArray());
    }

    public function testStructuredInputItemsSerializeRecursively(): void
    {
        $request = new CreateResponseRequest(
            model: 'openai/gpt-4o',
            input: [
                InputMessage::user([
                    new InputTextPart('Describe this picture'),
                    new InputImagePart(imageUrl: 'https://example.com/cat.png', detail: 'high'),
                ]),
                new InputFunctionCall(
                    callId: 'call-1',
                    name: 'get_weather',
                    arguments: '{"city":"Paris"}',
                ),
                new InputFunctionCallOutput(
                    callId: 'call-1',
                    output: '{"temp":"18C"}',
                ),
            ],
        );

        $data = $request->toArray();

        $this->assertSame('openai/gpt-4o', $data['model']);
        $this->assertIsArray($data['input']);
        $this->assertCount(3, $data['input']);

        $this->assertSame('message', $data['input'][0]['type']);
        $this->assertSame('user', $data['input'][0]['role']);
        $this->assertSame('input_text', $data['input'][0]['content'][0]['type']);
        $this->assertSame('Describe this picture', $data['input'][0]['content'][0]['text']);
        $this->assertSame('input_image', $data['input'][0]['content'][1]['type']);
        $this->assertSame('https://example.com/cat.png', $data['input'][0]['content'][1]['image_url']);
        $this->assertSame('high', $data['input'][0]['content'][1]['detail']);

        $this->assertSame('function_call', $data['input'][1]['type']);
        $this->assertSame('call-1', $data['input'][1]['call_id']);
        $this->assertSame('get_weather', $data['input'][1]['name']);
        $this->assertSame('{"city":"Paris"}', $data['input'][1]['arguments']);

        $this->assertSame('function_call_output', $data['input'][2]['type']);
        $this->assertSame('call-1', $data['input'][2]['call_id']);
        $this->assertSame('{"temp":"18C"}', $data['input'][2]['output']);
    }

    public function testOptionalFieldsAreAppendedWhenSet(): void
    {
        $request = new CreateResponseRequest(
            model: 'openai/gpt-4o',
            input: 'Hi',
            tools: [['type' => 'function', 'name' => 'noop']],
            toolChoice: 'auto',
            parallelToolCalls: false,
            temperature: 0.7,
            topP: 0.9,
            maxOutputTokens: 512,
            instructions: 'Be concise',
            previousResponseId: 'resp-prev',
            stream: false,
            metadata: ['trace_id' => 'abc'],
        );

        $data = $request->toArray();

        $this->assertSame([['type' => 'function', 'name' => 'noop']], $data['tools']);
        $this->assertSame('auto', $data['tool_choice']);
        $this->assertFalse($data['parallel_tool_calls']);
        $this->assertSame(0.7, $data['temperature']);
        $this->assertSame(0.9, $data['top_p']);
        $this->assertSame(512, $data['max_output_tokens']);
        $this->assertSame('Be concise', $data['instructions']);
        $this->assertSame('resp-prev', $data['previous_response_id']);
        $this->assertFalse($data['stream']);
        $this->assertSame(['trace_id' => 'abc'], $data['metadata']);
    }

    public function testExtrasAreMergedAndCanOverride(): void
    {
        $request = new CreateResponseRequest(
            model: 'openai/gpt-4o',
            input: 'Hi',
            extras: [
                'reasoning' => ['effort' => 'high'],
                'provider' => ['order' => ['anthropic']],
            ],
        );

        $data = $request->toArray();
        $this->assertSame(['effort' => 'high'], $data['reasoning']);
        $this->assertSame(['order' => ['anthropic']], $data['provider']);
    }

    public function testEmptyModelThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CreateResponseRequest(model: '', input: 'Hi');
    }

    public function testEmptyStringInputThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CreateResponseRequest(model: 'openai/gpt-4o', input: '');
    }

    public function testEmptyArrayInputThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CreateResponseRequest(model: 'openai/gpt-4o', input: []);
    }

    public function testInputAcceptsRawArrayItemsForOutputReplay(): void
    {
        $request = new CreateResponseRequest(
            model: 'openai/gpt-4o',
            input: [
                InputMessage::user('What did you answer?'),
                [
                    'type' => 'message',
                    'role' => 'assistant',
                    'id' => 'msg-prev',
                    'content' => [['type' => 'output_text', 'text' => 'Hello!', 'annotations' => []]],
                ],
            ],
        );

        $data = $request->toArray();

        $this->assertCount(2, $data['input']);
        $this->assertSame('user', $data['input'][0]['role']);
        $this->assertSame('assistant', $data['input'][1]['role']);
        $this->assertSame('msg-prev', $data['input'][1]['id']);
        $this->assertSame('output_text', $data['input'][1]['content'][0]['type']);
    }

    public function testInputMessageRoundTripsWithId(): void
    {
        $msg = new InputMessage(role: 'user', content: 'hello', id: 'msg-abc');
        $data = $msg->toArray();
        $this->assertSame('msg-abc', $data['id']);
        $this->assertSame('user', $data['role']);
    }

    public function testOutputMessageRoundTrip(): void
    {
        $msg = new OutputMessage(
            content: [['type' => 'output_text', 'text' => 'Hi.', 'annotations' => []]],
            id: 'msg-123',
            status: 'completed',
        );

        $this->assertSame([
            'type' => 'message',
            'role' => 'assistant',
            'content' => [['type' => 'output_text', 'text' => 'Hi.', 'annotations' => []]],
            'id' => 'msg-123',
            'status' => 'completed',
        ], $msg->toArray());
    }

    public function testOutputMessageRejectsUnknownStatus(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new OutputMessage(content: 'hi', status: 'finalized');
    }

    public function testInputMessageRejectsInvalidRole(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new InputMessage(role: 'robot', content: 'hi');
    }

    public function testInputMessageRejectsInvalidPhase(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new InputMessage(role: 'assistant', content: 'hi', phase: 'nonsense');
    }

    public function testInputImagePartRejectsInvalidDetail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new InputImagePart(imageUrl: 'https://example.com/a.png', detail: 'ultra');
    }

    public function testInputFilePartRequiresASource(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new InputFilePart();
    }

    public function testInputAudioPartRejectsInvalidFormat(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new InputAudioPart(data: 'base64', format: 'ogg');
    }

    public function testInputVideoPartRoundTrip(): void
    {
        $part = new InputVideoPart(videoUrl: 'data:video/mp4;base64,AAA');
        $this->assertSame([
            'type' => 'input_video',
            'video_url' => 'data:video/mp4;base64,AAA',
        ], $part->toArray());
    }

    public function testInputReasoningRoundTrip(): void
    {
        $reasoning = new InputReasoning(
            id: 'reasoning-1',
            summary: [['type' => 'summary_text', 'text' => 'Quick plan']],
            encryptedContent: 'opaque',
            signature: 'sig',
            status: 'completed',
        );

        $this->assertSame([
            'type' => 'reasoning',
            'id' => 'reasoning-1',
            'summary' => [['type' => 'summary_text', 'text' => 'Quick plan']],
            'signature' => 'sig',
            'status' => 'completed',
            'encrypted_content' => 'opaque',
        ], $reasoning->toArray());
    }

    public function testInputMessageStaticConstructors(): void
    {
        $this->assertSame('user', InputMessage::user('hi')->role);
        $this->assertSame('system', InputMessage::system('be nice')->role);
        $this->assertSame('assistant', InputMessage::assistant('ok')->role);
        $this->assertSame('developer', InputMessage::developer('debug')->role);
    }

    public function testTypedToolsAndPluginsAreSerialized(): void
    {
        $request = new CreateResponseRequest(
            model: 'openai/gpt-4o',
            input: 'Hi',
            tools: [new FunctionTool(name: 'pick', parameters: ['type' => 'object'])],
            plugins: [new WebSearchPlugin(maxResults: 2)],
        );

        $data = $request->toArray();
        $this->assertSame('function', $data['tools'][0]['type']);
        $this->assertSame('pick', $data['tools'][0]['name']);
        $this->assertSame('web', $data['plugins'][0]['id']);
        $this->assertSame(2, $data['plugins'][0]['max_results']);
    }

    public function testTypedToolsCanBeMixedWithRawArrays(): void
    {
        $request = new CreateResponseRequest(
            model: 'openai/gpt-4o',
            input: 'Hi',
            tools: [
                new FunctionTool(name: 'pick', parameters: []),
                ['type' => 'shell'],
            ],
        );

        $data = $request->toArray();
        $this->assertCount(2, $data['tools']);
        $this->assertSame('function', $data['tools'][0]['type']);
        $this->assertSame('shell', $data['tools'][1]['type']);
    }

    public function testTypedConfigsAreSerialized(): void
    {
        $request = new CreateResponseRequest(
            model: 'openai/gpt-4o',
            input: 'Hi',
            toolChoice: ToolChoice::required(),
            reasoning: new ReasoningConfig(effort: ReasoningEffort::High),
            text: new TextExtendedConfig(format: ['type' => 'json_object']),
            provider: new ProviderPreferences(order: ['anthropic']),
            trace: new TraceConfig(traceId: 'tr-1'),
            prompt: new StoredPromptTemplate(id: 'pt-1'),
        );

        $data = $request->toArray();
        $this->assertSame('required', $data['tool_choice']);
        $this->assertSame(['effort' => 'high'], $data['reasoning']);
        $this->assertSame(['format' => ['type' => 'json_object']], $data['text']);
        $this->assertSame(['order' => ['anthropic']], $data['provider']);
        $this->assertSame(['trace_id' => 'tr-1'], $data['trace']);
        $this->assertSame(['id' => 'pt-1'], $data['prompt']);
    }

    public function testIncludeAcceptsEnumList(): void
    {
        $request = new CreateResponseRequest(
            model: 'openai/gpt-4o',
            input: 'Hi',
            include: [
                ResponseIncludes::ReasoningEncryptedContent,
                ResponseIncludes::FileSearchCallResults,
            ],
        );

        $this->assertSame([
            'reasoning.encrypted_content',
            'file_search_call.results',
        ], $request->toArray()['include']);
    }

    public function testIncludeRejectsNonEnumEntry(): void
    {
        $this->expectException(InvalidArgumentException::class);
        /** @phpstan-ignore-next-line intentional bad input */
        new CreateResponseRequest(
            model: 'openai/gpt-4o',
            input: 'Hi',
            include: ['totally.fake.field'],
        );
    }

    public function testTruncationAcceptsEnum(): void
    {
        $req1 = new CreateResponseRequest(model: 'm', input: 'i', truncation: Truncation::Auto);
        $this->assertSame('auto', $req1->toArray()['truncation']);

        $req2 = new CreateResponseRequest(model: 'm', input: 'i', truncation: Truncation::Disabled);
        $this->assertSame('disabled', $req2->toArray()['truncation']);
    }

    public function testModelsFallbackChain(): void
    {
        $req = new CreateResponseRequest(model: 'a', input: 'i', models: ['a', 'b', 'c']);
        $this->assertSame(['a', 'b', 'c'], $req->toArray()['models']);
    }

    public function testEmptyModelsArrayThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CreateResponseRequest(model: 'a', input: 'i', models: []);
    }

    public function testModalitiesAcceptsEnumList(): void
    {
        $req = new CreateResponseRequest(
            model: 'm',
            input: 'i',
            modalities: [OutputModality::Text, OutputModality::Image],
        );
        $this->assertSame(['text', 'image'], $req->toArray()['modalities']);
    }

    public function testModalitiesRejectsNonEnumValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        /** @phpstan-ignore-next-line intentional bad input */
        new CreateResponseRequest(model: 'm', input: 'i', modalities: ['holographic']);
    }

    public function testServiceTierEnumSerializes(): void
    {
        $req = new CreateResponseRequest(
            model: 'm',
            input: 'i',
            serviceTier: ServiceTier::Priority,
        );
        $this->assertSame('priority', $req->toArray()['service_tier']);
    }

    public function testNewRequestFieldsSerialize(): void
    {
        $req = new CreateResponseRequest(
            model: 'm',
            input: 'i',
            imageConfig: ['aspect_ratio' => '16:9'],
            background: true,
            safetyIdentifier: 'user-hash-1',
            sessionId: 'sess-abc',
            user: 'user-1',
            frequencyPenalty: 0.2,
            presencePenalty: -0.1,
            topK: 40,
            topLogprobs: 5,
            promptCacheKey: 'cache-1',
            maxToolCalls: 8,
        );

        $arr = $req->toArray();
        $this->assertSame(['aspect_ratio' => '16:9'], $arr['image_config']);
        $this->assertTrue($arr['background']);
        $this->assertSame('user-hash-1', $arr['safety_identifier']);
        $this->assertSame('sess-abc', $arr['session_id']);
        $this->assertSame('user-1', $arr['user']);
        $this->assertSame(0.2, $arr['frequency_penalty']);
        $this->assertSame(-0.1, $arr['presence_penalty']);
        $this->assertSame(40, $arr['top_k']);
        $this->assertSame(5, $arr['top_logprobs']);
        $this->assertSame('cache-1', $arr['prompt_cache_key']);
        $this->assertSame(8, $arr['max_tool_calls']);
    }

    public function testIdentifierLengthLimit(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CreateResponseRequest(model: 'm', input: 'i', user: str_repeat('a', 257));
    }

    public function testTopKMustBeNonNegative(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CreateResponseRequest(model: 'm', input: 'i', topK: -1);
    }

    public function testFrequencyPenaltyRejectsInfinity(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CreateResponseRequest(model: 'm', input: 'i', frequencyPenalty: INF);
    }

    public function testMetadataEnforcesEntryCount(): void
    {
        $metadata = [];
        for ($i = 0; $i < 17; $i++) {
            $metadata["k{$i}"] = 'v';
        }

        $this->expectException(InvalidArgumentException::class);
        new CreateResponseRequest(model: 'm', input: 'i', metadata: $metadata);
    }

    public function testMetadataEnforcesKeyLength(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CreateResponseRequest(
            model: 'm',
            input: 'i',
            metadata: [str_repeat('k', 65) => 'v'],
        );
    }

    public function testMetadataRejectsBracketsInKey(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CreateResponseRequest(model: 'm', input: 'i', metadata: ['foo[0]' => 'v']);
    }

    public function testMetadataRejectsNonStringValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        /** @phpstan-ignore-next-line intentional bad input */
        new CreateResponseRequest(model: 'm', input: 'i', metadata: ['k' => 123]);
    }

    public function testMetadataEnforcesValueLength(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CreateResponseRequest(
            model: 'm',
            input: 'i',
            metadata: ['k' => str_repeat('v', 513)],
        );
    }
}
