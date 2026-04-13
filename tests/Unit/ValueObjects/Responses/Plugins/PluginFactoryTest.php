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
}
