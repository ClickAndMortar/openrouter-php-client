<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\ValueObjects\Responses\Plugins;

use OpenRouter\Enums\Responses\Tools\SearchContextSize;
use OpenRouter\Enums\Responses\Tools\WebSearchEngine;
use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\ValueObjects\Responses\Plugins\AutoRouterPlugin;
use OpenRouter\ValueObjects\Responses\Plugins\ContextCompressionPlugin;
use OpenRouter\ValueObjects\Responses\Plugins\FileParserPlugin;
use OpenRouter\ValueObjects\Responses\Plugins\ModerationPlugin;
use OpenRouter\ValueObjects\Responses\Plugins\AutoBetaRouterPlugin;
use OpenRouter\ValueObjects\Responses\Plugins\FusionPlugin;
use OpenRouter\ValueObjects\Responses\Plugins\ParetoRouterPlugin;
use OpenRouter\ValueObjects\Responses\Plugins\WebFetchPlugin;
use OpenRouter\ValueObjects\Responses\Plugins\PluginFactory;
use OpenRouter\ValueObjects\Responses\Plugins\ResponseHealingPlugin;
use OpenRouter\ValueObjects\Responses\Plugins\UnknownPlugin;
use OpenRouter\ValueObjects\Responses\Plugins\WebSearchPlugin;
use PHPUnit\Framework\TestCase;

final class PluginFactoryTest extends TestCase
{
    public function testDispatchesEachKnownIdToCorrectClass(): void
    {
        $cases = [
            [AutoRouterPlugin::class, ['id' => 'auto-router']],
            [ModerationPlugin::class, ['id' => 'moderation']],
            [WebSearchPlugin::class, ['id' => 'web']],
            [FileParserPlugin::class, ['id' => 'file-parser']],
            [ResponseHealingPlugin::class, ['id' => 'response-healing']],
            [ContextCompressionPlugin::class, ['id' => 'context-compression']],
        ];

        foreach ($cases as [$class, $payload]) {
            $this->assertInstanceOf($class, PluginFactory::from($payload), 'id=' . $payload['id']);
        }
    }

    public function testUnknownIdFallsBackToUnknownPlugin(): void
    {
        $plugin = PluginFactory::from(['id' => 'brand-new-plugin', 'foo' => 'bar']);
        $this->assertInstanceOf(UnknownPlugin::class, $plugin);
        $this->assertSame('brand-new-plugin', $plugin->id());
        $this->assertSame(['id' => 'brand-new-plugin', 'foo' => 'bar'], $plugin->toArray());
    }

    public function testWebSearchPluginRoundTrip(): void
    {
        $plugin = new WebSearchPlugin(
            enabled: true,
            engine: WebSearchEngine::Exa,
            maxResults: 3,
            searchPrompt: 'Search the web',
            includeDomains: ['example.com'],
            excludeDomains: ['spam.com'],
            searchContextSize: SearchContextSize::Medium,
        );

        $arr = $plugin->toArray();
        $this->assertSame('web', $arr['id']);
        $this->assertTrue($arr['enabled']);
        $this->assertSame('exa', $arr['engine']);
        $this->assertSame(3, $arr['max_results']);
        $this->assertSame(['example.com'], $arr['include_domains']);
        $this->assertSame(['spam.com'], $arr['exclude_domains']);
        $this->assertSame('medium', $arr['search_context_size']);
    }

    public function testAutoRouterAllowedModelsHydrates(): void
    {
        $plugin = AutoRouterPlugin::from(['id' => 'auto-router', 'enabled' => true, 'allowed_models' => ['anthropic/*', 'openai/gpt-4o']]);
        $this->assertSame(['anthropic/*', 'openai/gpt-4o'], $plugin->allowedModels);
        $this->assertTrue($plugin->enabled);
    }

    public function testModerationPluginIsBare(): void
    {
        $plugin = new ModerationPlugin();
        $this->assertSame(['id' => 'moderation'], $plugin->toArray());
    }

    public function testWebSearchPluginAcceptsEnums(): void
    {
        $plugin = new WebSearchPlugin(
            engine: WebSearchEngine::Firecrawl,
            searchContextSize: SearchContextSize::Low,
        );
        $arr = $plugin->toArray();
        $this->assertSame('firecrawl', $arr['engine']);
        $this->assertSame('low', $arr['search_context_size']);
    }

    public function testWebSearchPluginFromRejectsUnknownEngine(): void
    {
        $this->expectException(InvalidArgumentException::class);
        WebSearchPlugin::from(['id' => 'web', 'engine' => 'unknown-engine']);
    }

    public function testFactoryResolvesAutoBetaRouterPlugin(): void
    {
        $payload = [
            'id' => 'auto-beta-router',
            'enabled' => true,
            'cost_tier' => 'high',
            'cost_quality_tradeoff' => 3,
            'allowed_models' => ['anthropic/*'],
            'excluded_models' => ['openai/gpt-3.5-turbo'],
        ];

        $plugin = PluginFactory::from($payload);

        $this->assertInstanceOf(AutoBetaRouterPlugin::class, $plugin);
        $this->assertSame('auto-beta-router', $plugin->id());
        $this->assertSame('high', $plugin->costTier);
        $this->assertSame(3, $plugin->costQualityTradeoff);
        $this->assertSame(['anthropic/*'], $plugin->allowedModels);
        $this->assertSame(['openai/gpt-3.5-turbo'], $plugin->excludedModels);
        $this->assertSame($payload, $plugin->toArray());
    }

    public function testFactoryResolvesFusionPlugin(): void
    {
        $payload = [
            'id' => 'fusion',
            'enabled' => true,
            'model' => 'openai/gpt-4o',
            'preset' => 'general-high',
            'max_tool_calls' => 4,
            'analysis_models' => ['anthropic/claude-sonnet-4'],
            'tools' => [['type' => 'function', 'name' => 'lookup']],
        ];

        $plugin = PluginFactory::from($payload);

        $this->assertInstanceOf(FusionPlugin::class, $plugin);
        $this->assertSame('fusion', $plugin->id());
        $this->assertSame('openai/gpt-4o', $plugin->model);
        $this->assertSame('general-high', $plugin->preset);
        $this->assertSame(4, $plugin->maxToolCalls);
        $this->assertSame($payload, $plugin->toArray());
    }

    public function testFactoryResolvesParetoRouterPlugin(): void
    {
        $payload = [
            'id' => 'pareto-router',
            'enabled' => true,
            'max_price' => 2.5,
            'min_coding_score' => 0.8,
            'price_source' => 'weighted_avg',
        ];

        $plugin = PluginFactory::from($payload);

        $this->assertInstanceOf(ParetoRouterPlugin::class, $plugin);
        $this->assertSame('pareto-router', $plugin->id());
        $this->assertSame(2.5, $plugin->maxPrice);
        $this->assertSame(0.8, $plugin->minCodingScore);
        $this->assertSame('weighted_avg', $plugin->priceSource);
        $this->assertSame($payload, $plugin->toArray());
    }

    public function testFactoryResolvesWebFetchPlugin(): void
    {
        $payload = [
            'id' => 'web-fetch',
            'max_uses' => 3,
            'max_content_tokens' => 4096,
            'allowed_domains' => ['example.com'],
            'blocked_domains' => ['spam.example'],
        ];

        $plugin = PluginFactory::from($payload);

        $this->assertInstanceOf(WebFetchPlugin::class, $plugin);
        $this->assertSame('web-fetch', $plugin->id());
        $this->assertSame(3, $plugin->maxUses);
        $this->assertSame(4096, $plugin->maxContentTokens);
        $this->assertSame($payload, $plugin->toArray());
    }

    public function testAutoRouterPluginSupportsCostAndExclusionOptions(): void
    {
        $payload = [
            'id' => 'auto-router',
            'enabled' => true,
            'allowed_models' => ['anthropic/*'],
            'excluded_models' => ['openai/gpt-3.5-turbo'],
            'cost_tier' => 'max',
            'cost_quality_tradeoff' => 5,
            'pin_model' => true,
        ];

        $plugin = PluginFactory::from($payload);

        $this->assertSame(['openai/gpt-3.5-turbo'], $plugin->excludedModels);
        $this->assertSame('max', $plugin->costTier);
        $this->assertSame(5, $plugin->costQualityTradeoff);
        $this->assertTrue($plugin->pinModel);
        $this->assertSame($payload, $plugin->toArray());
    }
}
