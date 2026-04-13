<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\ValueObjects\Responses\Config;

use OpenRouter\Enums\Responses\ReasoningEffort;
use OpenRouter\Enums\Responses\ReasoningSummaryVerbosity;
use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\ValueObjects\Responses\Config\ProviderPreferences;
use OpenRouter\ValueObjects\Responses\Config\ReasoningConfig;
use OpenRouter\ValueObjects\Responses\Config\StoredPromptTemplate;
use OpenRouter\ValueObjects\Responses\Config\TextExtendedConfig;
use OpenRouter\ValueObjects\Responses\Config\ToolChoice;
use OpenRouter\ValueObjects\Responses\Config\TraceConfig;
use PHPUnit\Framework\TestCase;

final class ConfigVOsTest extends TestCase
{
    public function testReasoningConfigStripsNulls(): void
    {
        $cfg = new ReasoningConfig(enabled: true, effort: ReasoningEffort::High);
        $this->assertSame(['enabled' => true, 'effort' => 'high'], $cfg->toArray());

        $empty = new ReasoningConfig();
        $this->assertSame([], $empty->toArray());
    }

    public function testReasoningConfigSerializesEnums(): void
    {
        $cfg = new ReasoningConfig(
            effort: ReasoningEffort::Minimal,
            summary: ReasoningSummaryVerbosity::Detailed,
        );
        $this->assertSame(['effort' => 'minimal', 'summary' => 'detailed'], $cfg->toArray());
    }

    public function testReasoningConfigFromRejectsUnknownEffortString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ReasoningConfig::from(['effort' => 'unreasonable']);
    }

    public function testProviderPreferencesRoundTrip(): void
    {
        $prefs = new ProviderPreferences(
            order: ['anthropic', 'openai'],
            allowFallbacks: true,
            dataCollection: 'deny',
            requireParameters: false,
            zdr: true,
        );

        $arr = $prefs->toArray();
        $this->assertSame(['anthropic', 'openai'], $arr['order']);
        $this->assertTrue($arr['allow_fallbacks']);
        $this->assertSame('deny', $arr['data_collection']);
        $this->assertFalse($arr['require_parameters']);
        $this->assertTrue($arr['zdr']);
        $this->assertArrayNotHasKey('only', $arr);
    }

    public function testTraceConfigPreservesCustomMetadata(): void
    {
        $trace = TraceConfig::from([
            'trace_id' => 'tr-1',
            'trace_name' => 'invoice-flow',
            'user_id' => 42,
            'tenant' => 'acme',
        ]);

        $arr = $trace->toArray();
        $this->assertSame('tr-1', $arr['trace_id']);
        $this->assertSame('invoice-flow', $arr['trace_name']);
        $this->assertSame(42, $arr['user_id']);
        $this->assertSame('acme', $arr['tenant']);
    }

    public function testTextExtendedConfigPassesFormatThrough(): void
    {
        $cfg = new TextExtendedConfig(format: ['type' => 'json_object'], verbosity: 'concise');
        $this->assertSame([
            'format' => ['type' => 'json_object'],
            'verbosity' => 'concise',
        ], $cfg->toArray());
    }

    public function testStoredPromptTemplateRequiresId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new StoredPromptTemplate(id: '');
    }

    public function testStoredPromptTemplateRoundTrip(): void
    {
        $tpl = new StoredPromptTemplate(id: 'pt_invoice_v3', variables: ['city' => 'Paris']);
        $this->assertSame(['id' => 'pt_invoice_v3', 'variables' => ['city' => 'Paris']], $tpl->toArray());
    }

    public function testToolChoiceStringFactories(): void
    {
        $this->assertSame('auto', ToolChoice::auto()->toArray());
        $this->assertSame('required', ToolChoice::required()->toArray());
        $this->assertSame('none', ToolChoice::none()->toArray());
    }

    public function testToolChoiceAllowedToolsRoundTrip(): void
    {
        $tc = ToolChoice::allowed([['type' => 'function', 'name' => 'pick']], 'required');
        $this->assertSame([
            'type' => 'allowed_tools',
            'mode' => 'required',
            'tools' => [['type' => 'function', 'name' => 'pick']],
        ], $tc->toArray());
    }

    public function testToolChoiceFromInvalidStringThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ToolChoice::from('bogus');
    }

    public function testToolChoiceAllowedRejectsBadMode(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ToolChoice::allowed([], 'sometimes');
    }

    public function testToolChoiceFunctionRoundTrip(): void
    {
        $tc = ToolChoice::function('get_weather');
        $this->assertSame(['type' => 'function', 'name' => 'get_weather'], $tc->toArray());

        $from = ToolChoice::from(['type' => 'function', 'name' => 'get_weather']);
        $this->assertSame($tc->toArray(), $from->toArray());
    }

    public function testToolChoiceFunctionRejectsEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ToolChoice::function('');
    }

    public function testToolChoiceHostedRoundTrip(): void
    {
        $tc = ToolChoice::hosted('web_search_preview');
        $this->assertSame(['type' => 'web_search_preview'], $tc->toArray());

        $dated = ToolChoice::from(['type' => 'web_search_preview_2025_03_11']);
        $this->assertSame(['type' => 'web_search_preview_2025_03_11'], $dated->toArray());
    }

    public function testToolChoiceHostedRejectsUnknownType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ToolChoice::hosted('web_search');
    }

    public function testToolChoiceFromArrayRejectsUnknownType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ToolChoice::from(['type' => 'mystery']);
    }
}
