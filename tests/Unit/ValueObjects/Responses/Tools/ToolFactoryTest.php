<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\ValueObjects\Responses\Tools;

use OpenRouter\Enums\Responses\Tools\ComputerEnvironment;
use OpenRouter\Enums\Responses\Tools\ImageQuality;
use OpenRouter\Enums\Responses\Tools\SearchContextSize;
use OpenRouter\Enums\Responses\Tools\WebSearchEngine;
use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\ValueObjects\Responses\Tools\ApplyPatchServerTool;
use OpenRouter\ValueObjects\Responses\Tools\CodeInterpreterServerTool;
use OpenRouter\ValueObjects\Responses\Tools\CodexLocalShellTool;
use OpenRouter\ValueObjects\Responses\Tools\ComputerUseServerTool;
use OpenRouter\ValueObjects\Responses\Tools\CustomTool;
use OpenRouter\ValueObjects\Responses\Tools\DatetimeServerTool;
use OpenRouter\ValueObjects\Responses\Tools\FileSearchServerTool;
use OpenRouter\ValueObjects\Responses\Tools\FunctionTool;
use OpenRouter\ValueObjects\Responses\Tools\ImageGenerationServerTool;
use OpenRouter\ValueObjects\Responses\Tools\McpServerTool;
use OpenRouter\ValueObjects\Responses\Tools\ShellServerTool;
use OpenRouter\ValueObjects\Responses\Tools\ToolFactory;
use OpenRouter\ValueObjects\Responses\Tools\UnknownTool;
use OpenRouter\ValueObjects\Responses\Tools\WebSearchLegacyServerTool;
use OpenRouter\ValueObjects\Responses\Tools\WebSearchPreview20250311ServerTool;
use OpenRouter\ValueObjects\Responses\Tools\WebSearchPreviewServerTool;
use OpenRouter\ValueObjects\Responses\Tools\WebSearchServerTool;
use OpenRouter\ValueObjects\Responses\Tools\WebSearchServerToolOpenRouter;
use PHPUnit\Framework\TestCase;

final class ToolFactoryTest extends TestCase
{
    public function testDispatchesEachKnownTypeToCorrectClass(): void
    {
        $cases = [
            [FunctionTool::class, ['type' => 'function', 'name' => 'noop', 'parameters' => []]],
            [WebSearchServerTool::class, ['type' => 'web_search_2025_08_26']],
            [WebSearchPreviewServerTool::class, ['type' => 'web_search_preview']],
            [WebSearchPreview20250311ServerTool::class, ['type' => 'web_search_preview_2025_03_11']],
            [WebSearchLegacyServerTool::class, ['type' => 'web_search']],
            [WebSearchServerToolOpenRouter::class, ['type' => 'openrouter:web_search']],
            [FileSearchServerTool::class, ['type' => 'file_search', 'vector_store_ids' => ['vs-1']]],
            [McpServerTool::class, ['type' => 'mcp', 'server_label' => 'srv']],
            [ImageGenerationServerTool::class, ['type' => 'image_generation']],
            [CodeInterpreterServerTool::class, ['type' => 'code_interpreter', 'container' => 'auto']],
            [ComputerUseServerTool::class, ['type' => 'computer_use_preview', 'display_width' => 1024, 'display_height' => 768, 'environment' => 'browser']],
            [ShellServerTool::class, ['type' => 'shell']],
            [CodexLocalShellTool::class, ['type' => 'local_shell']],
            [ApplyPatchServerTool::class, ['type' => 'apply_patch']],
            [DatetimeServerTool::class, ['type' => 'openrouter:datetime']],
            [CustomTool::class, ['type' => 'custom', 'name' => 'pick']],
        ];

        foreach ($cases as [$class, $payload]) {
            $this->assertInstanceOf($class, ToolFactory::from($payload), 'type=' . $payload['type']);
        }
    }

    public function testUnknownTypeFallsBackToUnknownTool(): void
    {
        $tool = ToolFactory::from(['type' => 'something_brand_new', 'foo' => 'bar']);
        $this->assertInstanceOf(UnknownTool::class, $tool);
        $this->assertSame('something_brand_new', $tool->type());
        $this->assertSame(['type' => 'something_brand_new', 'foo' => 'bar'], $tool->toArray());
    }

    public function testFunctionToolRoundTrip(): void
    {
        $tool = new FunctionTool(
            name: 'get_weather',
            parameters: ['type' => 'object', 'properties' => ['city' => ['type' => 'string']]],
            description: 'Looks up the weather',
            strict: true,
        );

        $arr = $tool->toArray();
        $this->assertSame('function', $arr['type']);
        $this->assertSame('get_weather', $arr['name']);
        $this->assertSame(['type' => 'object', 'properties' => ['city' => ['type' => 'string']]], $arr['parameters']);
        $this->assertSame('Looks up the weather', $arr['description']);
        $this->assertTrue($arr['strict']);
    }

    public function testFunctionToolRequiresName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new FunctionTool(name: '', parameters: []);
    }

    public function testFileSearchRequiresVectorStoreIds(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new FileSearchServerTool(vectorStoreIds: []);
    }

    public function testMcpServerToolRoundTrip(): void
    {
        $tool = McpServerTool::from([
            'type' => 'mcp',
            'server_label' => 'gh',
            'server_url' => 'https://mcp.example.com',
            'authorization' => 'Bearer abc',
            'headers' => ['x-trace' => 'xyz'],
            'allowed_tools' => ['read_file', 'write_file'],
            'require_approval' => 'never',
        ]);

        $arr = $tool->toArray();
        $this->assertSame('mcp', $arr['type']);
        $this->assertSame('gh', $arr['server_label']);
        $this->assertSame('Bearer abc', $arr['authorization']);
        $this->assertSame(['read_file', 'write_file'], $arr['allowed_tools']);
        $this->assertSame('never', $arr['require_approval']);
    }

    public function testWebSearchServerToolRoundTrip(): void
    {
        $tool = new WebSearchServerTool(
            engine: WebSearchEngine::Exa,
            maxResults: 5,
            searchContextSize: SearchContextSize::Medium,
            userLocation: ['city' => 'Paris'],
        );

        $arr = $tool->toArray();
        $this->assertSame('web_search_2025_08_26', $arr['type']);
        $this->assertSame('exa', $arr['engine']);
        $this->assertSame(5, $arr['max_results']);
        $this->assertSame('medium', $arr['search_context_size']);
        $this->assertSame(['city' => 'Paris'], $arr['user_location']);
    }

    public function testDatetimeServerToolWrapsTimezoneInParameters(): void
    {
        $tool = new DatetimeServerTool(timezone: 'Europe/Paris');
        $this->assertSame([
            'type' => 'openrouter:datetime',
            'parameters' => ['timezone' => 'Europe/Paris'],
        ], $tool->toArray());

        $bare = new DatetimeServerTool();
        $this->assertSame(['type' => 'openrouter:datetime'], $bare->toArray());
    }

    public function testComputerUseFromRejectsUnknownEnvironment(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ComputerUseServerTool::from([
            'display_width' => 1024,
            'display_height' => 768,
            'environment' => 'amiga',
        ]);
    }

    public function testImageGenerationServerToolRoundTrip(): void
    {
        $tool = new ImageGenerationServerTool(
            model: 'gpt-image-1',
            quality: ImageQuality::High,
            size: '1024x1024',
            outputFormat: 'png',
            background: 'transparent',
        );

        $arr = $tool->toArray();
        $this->assertSame('image_generation', $arr['type']);
        $this->assertSame('gpt-image-1', $arr['model']);
        $this->assertSame('high', $arr['quality']);
        $this->assertSame('transparent', $arr['background']);
    }

    public function testComputerUseServerToolRoundTripViaEnum(): void
    {
        $tool = new ComputerUseServerTool(
            displayWidth: 1024,
            displayHeight: 768,
            environment: ComputerEnvironment::Browser,
        );

        $this->assertSame('browser', $tool->toArray()['environment']);
    }

    public function testImageGenerationServerToolFromRejectsUnknownQuality(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ImageGenerationServerTool::from(['quality' => 'bogus']);
    }

    public function testWebSearchServerToolFromRejectsUnknownEngine(): void
    {
        $this->expectException(InvalidArgumentException::class);
        WebSearchServerTool::from(['engine' => 'nonsense']);
    }
}
