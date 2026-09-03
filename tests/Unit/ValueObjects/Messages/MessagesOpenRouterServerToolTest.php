<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\ValueObjects\Messages;

use OpenRouter\ValueObjects\Messages\Tools\MessagesOpenRouterServerTool;
use OpenRouter\ValueObjects\Messages\Tools\MessagesToolFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class MessagesOpenRouterServerToolTest extends TestCase
{
    /**
     * @return iterable<string, array{string}>
     */
    public static function typeProvider(): iterable
    {
        foreach (MessagesOpenRouterServerTool::TYPES as $type) {
            yield $type => [$type];
        }
    }

    public function testCoversEveryNewMessagesSideType(): void
    {
        $this->assertSame(
            [
                'openrouter:bash',
                'openrouter:experimental__search_models',
                'openrouter:image_generation',
                'openrouter:shell',
                'openrouter:tool_search',
                'openrouter:web_fetch',
            ],
            MessagesOpenRouterServerTool::TYPES,
        );
    }

    #[DataProvider('typeProvider')]
    public function testFactoryResolvesOpenRouterServerTools(string $type): void
    {
        $tool = MessagesToolFactory::from(['type' => $type]);

        $this->assertInstanceOf(MessagesOpenRouterServerTool::class, $tool);
        $this->assertSame($type, $tool->type());
    }

    public function testSerializesParameters(): void
    {
        $tool = MessagesOpenRouterServerTool::toolSearch(['max_results' => 5]);

        $this->assertSame(
            ['type' => 'openrouter:tool_search', 'parameters' => ['max_results' => 5]],
            $tool->toArray(),
        );
    }

    public function testOmitsEmptyParameters(): void
    {
        $this->assertSame(
            ['type' => 'openrouter:shell'],
            MessagesOpenRouterServerTool::shell()->toArray(),
        );
    }

    public function testRoundTripsThroughTheFactory(): void
    {
        $payload = ['type' => 'openrouter:bash', 'parameters' => ['engine' => 'container']];

        $this->assertSame($payload, MessagesToolFactory::from($payload)->toArray());
    }
}
