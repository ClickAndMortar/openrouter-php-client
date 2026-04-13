<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\ValueObjects\Messages;

use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\Responses\Messages\Deltas\CitationsDelta;
use OpenRouter\Responses\Messages\Deltas\DeltaFactory;
use OpenRouter\Responses\Messages\Deltas\InputJsonDelta;
use OpenRouter\Responses\Messages\Deltas\TextDelta;
use OpenRouter\Responses\Messages\Deltas\ThinkingDelta;
use OpenRouter\Responses\Messages\Deltas\UnknownDelta;
use OpenRouter\ValueObjects\Messages\Citations\CharLocationCitation;
use OpenRouter\ValueObjects\Messages\Citations\CitationFactory;
use OpenRouter\ValueObjects\Messages\Citations\WebSearchResultLocationCitation;
use OpenRouter\ValueObjects\Messages\Config\MessagesOutputConfig;
use OpenRouter\ValueObjects\Messages\Config\MessagesThinkingConfig;
use OpenRouter\ValueObjects\Messages\Config\MessagesToolChoice;
use OpenRouter\ValueObjects\Messages\ContextManagement\ClearThinkingEdit;
use OpenRouter\ValueObjects\Messages\ContextManagement\ClearToolUsesEdit;
use OpenRouter\ValueObjects\Messages\ContextManagement\CompactEdit;
use OpenRouter\ValueObjects\Messages\ContextManagement\ContextManagement;
use OpenRouter\ValueObjects\Messages\ContextManagement\ContextManagementEditFactory;
use OpenRouter\ValueObjects\Messages\Content\CompactionBlock;
use OpenRouter\ValueObjects\Messages\Content\ContainerUploadBlock;
use OpenRouter\ValueObjects\Messages\Content\ContentBlockFactory;
use OpenRouter\ValueObjects\Messages\Content\DocumentBlock;
use OpenRouter\ValueObjects\Messages\Content\ImageBlock;
use OpenRouter\ValueObjects\Messages\Content\MessagesCacheControl;
use OpenRouter\ValueObjects\Messages\Content\RedactedThinkingBlock;
use OpenRouter\ValueObjects\Messages\Content\ServerToolUseBlock;
use OpenRouter\ValueObjects\Messages\Content\TextBlock;
use OpenRouter\ValueObjects\Messages\Content\ThinkingBlock;
use OpenRouter\ValueObjects\Messages\Content\ToolResultBlock;
use OpenRouter\ValueObjects\Messages\Content\ToolUseBlock;
use OpenRouter\ValueObjects\Messages\Content\UnknownContentBlock;
use OpenRouter\ValueObjects\Messages\Content\WebSearchToolResultBlock;
use OpenRouter\ValueObjects\Messages\Tools\BashTool;
use OpenRouter\ValueObjects\Messages\Tools\CustomTool;
use OpenRouter\ValueObjects\Messages\Tools\DatetimeTool;
use OpenRouter\ValueObjects\Messages\Tools\MessagesToolFactory;
use OpenRouter\ValueObjects\Messages\Tools\OpenRouterWebSearchTool;
use OpenRouter\ValueObjects\Messages\Tools\TextEditorTool;
use OpenRouter\ValueObjects\Messages\Tools\UnknownMessagesTool;
use OpenRouter\ValueObjects\Messages\Tools\WebSearchTool;
use PHPUnit\Framework\TestCase;

final class MessagesTypedVOsTest extends TestCase
{
    // ---------- Cache control ----------

    public function testMessagesCacheControlRejectsUnknownType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new MessagesCacheControl('bogus');
    }

    public function testMessagesCacheControlSerializesWithTtl(): void
    {
        $cc = new MessagesCacheControl('ephemeral', '1h');
        $this->assertSame(['type' => 'ephemeral', 'ttl' => '1h'], $cc->toArray());
    }

    // ---------- Tool choice ----------

    public function testToolChoiceBuildersProduceExpectedPayloads(): void
    {
        $this->assertSame(['type' => 'auto'], MessagesToolChoice::auto()->toArray());
        $this->assertSame(
            ['type' => 'auto', 'disable_parallel_tool_use' => true],
            MessagesToolChoice::auto(true)->toArray(),
        );
        $this->assertSame(['type' => 'any'], MessagesToolChoice::any()->toArray());
        $this->assertSame(['type' => 'none'], MessagesToolChoice::none()->toArray());
        $this->assertSame(
            ['type' => 'tool', 'name' => 'get_weather'],
            MessagesToolChoice::tool('get_weather')->toArray(),
        );
    }

    public function testToolChoiceFromRequiresNameForToolType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        MessagesToolChoice::from(['type' => 'tool']);
    }

    public function testToolChoiceFromRejectsUnknownType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        MessagesToolChoice::from(['type' => 'bogus']);
    }

    // ---------- Thinking ----------

    public function testThinkingConfigBuilders(): void
    {
        $this->assertSame(
            ['type' => 'enabled', 'budget_tokens' => 1000],
            MessagesThinkingConfig::enabled(1000)->toArray(),
        );
        $this->assertSame(['type' => 'disabled'], MessagesThinkingConfig::disabled()->toArray());
        $this->assertSame(['type' => 'adaptive'], MessagesThinkingConfig::adaptive()->toArray());
    }

    public function testThinkingConfigEnabledRejectsZeroBudget(): void
    {
        $this->expectException(InvalidArgumentException::class);
        MessagesThinkingConfig::enabled(0);
    }

    // ---------- Output config ----------

    public function testOutputConfigJsonSchemaBuilder(): void
    {
        $schema = ['type' => 'object', 'properties' => ['x' => ['type' => 'string']]];
        $cfg = MessagesOutputConfig::jsonSchema($schema, 'high');

        $this->assertSame([
            'effort' => 'high',
            'format' => ['type' => 'json_schema', 'schema' => $schema],
        ], $cfg->toArray());
    }

    public function testOutputConfigRejectsUnknownEffort(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new MessagesOutputConfig(effort: 'insane');
    }

    // ---------- Tools ----------

    public function testCustomToolSerializes(): void
    {
        $t = new CustomTool(
            name: 'get_weather',
            inputSchema: ['type' => 'object'],
            description: 'Get weather',
            cacheControl: new MessagesCacheControl(),
        );
        $this->assertSame('custom', $t->type());
        $this->assertSame([
            'type' => 'custom',
            'name' => 'get_weather',
            'input_schema' => ['type' => 'object'],
            'description' => 'Get weather',
            'cache_control' => ['type' => 'ephemeral'],
        ], $t->toArray());
    }

    public function testCustomToolRejectsEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CustomTool(name: '');
    }

    public function testToolFactoryDispatchesAllVariants(): void
    {
        $this->assertInstanceOf(CustomTool::class, MessagesToolFactory::from([
            'type' => 'custom', 'name' => 'x', 'input_schema' => [],
        ]));
        $this->assertInstanceOf(BashTool::class, MessagesToolFactory::from(['type' => 'bash_20250124']));
        $this->assertInstanceOf(TextEditorTool::class, MessagesToolFactory::from(['type' => 'text_editor_20250124']));
        $this->assertInstanceOf(WebSearchTool::class, MessagesToolFactory::from(['type' => 'web_search_20250305']));
        $this->assertInstanceOf(WebSearchTool::class, MessagesToolFactory::from(['type' => 'web_search_20260209']));
        $this->assertInstanceOf(DatetimeTool::class, MessagesToolFactory::from(['type' => 'openrouter:datetime']));
        $this->assertInstanceOf(OpenRouterWebSearchTool::class, MessagesToolFactory::from([
            'type' => 'openrouter:web_search',
        ]));
        $this->assertInstanceOf(UnknownMessagesTool::class, MessagesToolFactory::from(['type' => 'new_tool_v999']));
    }

    public function testBashAndTextEditorToolsEmitCanonicalNames(): void
    {
        $this->assertSame(
            ['type' => 'bash_20250124', 'name' => 'bash'],
            (new BashTool())->toArray(),
        );
        $this->assertSame(
            ['type' => 'text_editor_20250124', 'name' => 'str_replace_editor'],
            (new TextEditorTool())->toArray(),
        );
    }

    public function testWebSearchToolSerializesAllowedCallersOnlyForNewVariant(): void
    {
        $newer = new WebSearchTool(
            version: WebSearchTool::VERSION_2026_02_09,
            allowedCallers: ['claude-code'],
        );
        $this->assertSame('claude-code', $newer->toArray()['allowed_callers'][0]);

        $older = new WebSearchTool(
            version: WebSearchTool::VERSION_2025_03_05,
            allowedCallers: ['claude-code'],
        );
        $this->assertArrayNotHasKey('allowed_callers', $older->toArray());
    }

    public function testDatetimeToolEmitsParametersOnlyWhenSet(): void
    {
        $this->assertSame(['type' => 'openrouter:datetime'], (new DatetimeTool())->toArray());
        $this->assertSame(
            ['type' => 'openrouter:datetime', 'parameters' => ['timezone' => 'UTC']],
            (new DatetimeTool('UTC'))->toArray(),
        );
    }

    // ---------- Content blocks ----------

    public function testTextBlockRoundTripsCitations(): void
    {
        $block = new TextBlock(
            text: 'Hello',
            citations: [
                new CharLocationCitation(
                    citedText: 'hi',
                    startCharIndex: 0,
                    endCharIndex: 2,
                    documentIndex: 0,
                    documentTitle: 'doc.txt',
                ),
            ],
        );
        $arr = $block->toArray();
        $this->assertSame('char_location', $arr['citations'][0]['type']);
        $this->assertSame('hi', $arr['citations'][0]['cited_text']);

        $round = TextBlock::from($arr);
        $this->assertCount(1, $round->citations);
        $this->assertInstanceOf(CharLocationCitation::class, $round->citations[0]);
    }

    public function testContentBlockFactoryDispatchesAllTypes(): void
    {
        $cases = [
            'text' => TextBlock::class,
            'image' => ImageBlock::class,
            'document' => DocumentBlock::class,
            'tool_use' => ToolUseBlock::class,
            'tool_result' => ToolResultBlock::class,
            'thinking' => ThinkingBlock::class,
            'redacted_thinking' => RedactedThinkingBlock::class,
            'server_tool_use' => ServerToolUseBlock::class,
            'web_search_tool_result' => WebSearchToolResultBlock::class,
            'container_upload' => ContainerUploadBlock::class,
            'compaction' => CompactionBlock::class,
            'future_unknown_type' => UnknownContentBlock::class,
        ];

        foreach ($cases as $type => $class) {
            $block = ContentBlockFactory::from(['type' => $type]);
            $this->assertInstanceOf($class, $block, "type=$type should be $class");
            $this->assertSame($type, $block->type());
        }
    }

    public function testToolUseBlockRoundTrip(): void
    {
        $block = new ToolUseBlock(
            id: 'toolu_123',
            name: 'get_weather',
            input: ['location' => 'Paris'],
        );
        $arr = $block->toArray();
        $this->assertSame(['type' => 'tool_use', 'id' => 'toolu_123', 'name' => 'get_weather', 'input' => ['location' => 'Paris']], $arr);

        $round = ToolUseBlock::from($arr);
        $this->assertSame('toolu_123', $round->id);
        $this->assertSame(['location' => 'Paris'], $round->input);
    }

    public function testToolResultBlockAcceptsStringAndArrayContent(): void
    {
        $stringResult = new ToolResultBlock(toolUseId: 'x', content: 'done');
        $this->assertSame('done', $stringResult->toArray()['content']);

        $arrayResult = new ToolResultBlock(
            toolUseId: 'x',
            content: [['type' => 'text', 'text' => 'result']],
            isError: true,
        );
        $this->assertTrue($arrayResult->toArray()['is_error']);
        $this->assertSame('text', $arrayResult->toArray()['content'][0]['type']);
    }

    public function testImageBlockBase64AndUrlBuilders(): void
    {
        $b64 = ImageBlock::base64('image/png', 'ZGF0YQ==');
        $this->assertSame('base64', $b64->source['type']);
        $this->assertSame('image/png', $b64->source['media_type']);

        $url = ImageBlock::url('https://example.com/img.png');
        $this->assertSame('url', $url->source['type']);
        $this->assertSame('https://example.com/img.png', $url->source['url']);
    }

    // ---------- Context management ----------

    public function testContextManagementEditFactory(): void
    {
        $this->assertInstanceOf(
            ClearToolUsesEdit::class,
            ContextManagementEditFactory::from(['type' => 'clear_tool_uses_20250919']),
        );
        $this->assertInstanceOf(
            ClearThinkingEdit::class,
            ContextManagementEditFactory::from(['type' => 'clear_thinking_20251015']),
        );
        $this->assertInstanceOf(
            CompactEdit::class,
            ContextManagementEditFactory::from(['type' => 'compact_20260112']),
        );
    }

    public function testContextManagementRoundTrip(): void
    {
        $cm = new ContextManagement([
            ClearThinkingEdit::keepAll(),
            new CompactEdit(instructions: 'keep it tight'),
        ]);

        $arr = $cm->toArray();
        $this->assertSame('clear_thinking_20251015', $arr['edits'][0]['type']);
        $this->assertSame(['type' => 'all'], $arr['edits'][0]['keep']);
        $this->assertSame('compact_20260112', $arr['edits'][1]['type']);
        $this->assertSame('keep it tight', $arr['edits'][1]['instructions']);

        $round = ContextManagement::from($arr);
        $this->assertCount(2, $round->edits);
    }

    // ---------- Citations ----------

    public function testCitationFactoryDispatchesAllTypes(): void
    {
        $this->assertInstanceOf(CharLocationCitation::class, CitationFactory::from(['type' => 'char_location']));
        $this->assertInstanceOf(
            WebSearchResultLocationCitation::class,
            CitationFactory::from(['type' => 'web_search_result_location']),
        );
    }

    // ---------- Deltas ----------

    public function testDeltaFactoryDispatchesAllTypes(): void
    {
        $this->assertInstanceOf(TextDelta::class, DeltaFactory::from(['type' => 'text_delta', 'text' => 'hi']));
        $this->assertInstanceOf(
            InputJsonDelta::class,
            DeltaFactory::from(['type' => 'input_json_delta', 'partial_json' => '{']),
        );
        $this->assertInstanceOf(
            ThinkingDelta::class,
            DeltaFactory::from(['type' => 'thinking_delta', 'thinking' => '...']),
        );
        $this->assertInstanceOf(
            CitationsDelta::class,
            DeltaFactory::from([
                'type' => 'citations_delta',
                'citation' => ['type' => 'char_location', 'cited_text' => 'x', 'start_char_index' => 0, 'end_char_index' => 1],
            ]),
        );
        $this->assertInstanceOf(UnknownDelta::class, DeltaFactory::from(['type' => 'new_delta']));
    }

    public function testInputJsonDeltaConcatenation(): void
    {
        $d1 = InputJsonDelta::from(['partial_json' => '{"loc":']);
        $d2 = InputJsonDelta::from(['partial_json' => '"Paris"}']);

        $this->assertSame('{"loc":"Paris"}', $d1->partialJson . $d2->partialJson);
    }
}
