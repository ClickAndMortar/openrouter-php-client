<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\ValueObjects\Chat;

use OpenRouter\ValueObjects\Chat\Tools\ChatOpenRouterServerTool;
use OpenRouter\ValueObjects\Chat\Tools\ChatToolFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ChatOpenRouterServerToolTest extends TestCase
{
    /**
     * @return iterable<string, array{string}>
     */
    public static function typeProvider(): iterable
    {
        foreach (ChatOpenRouterServerTool::TYPES as $type) {
            yield $type => [$type];
        }
    }

    public function testCoversEveryNewChatSideType(): void
    {
        $this->assertSame(
            [
                'openrouter:advisor',
                'openrouter:bash',
                'openrouter:experimental__search_models',
                'openrouter:files',
                'openrouter:fusion',
                'openrouter:image_generation',
                'openrouter:subagent',
                'openrouter:web_fetch',
            ],
            ChatOpenRouterServerTool::TYPES,
        );
    }

    #[DataProvider('typeProvider')]
    public function testFactoryResolvesOpenRouterServerTools(string $type): void
    {
        $tool = ChatToolFactory::from(['type' => $type]);

        $this->assertInstanceOf(ChatOpenRouterServerTool::class, $tool);
        $this->assertSame($type, $tool->type());
    }

    public function testSerializesParameters(): void
    {
        $tool = ChatOpenRouterServerTool::webFetch(['max_uses' => 2]);

        $this->assertSame(
            ['type' => 'openrouter:web_fetch', 'parameters' => ['max_uses' => 2]],
            $tool->toArray(),
        );
    }

    public function testOmitsEmptyParameters(): void
    {
        $this->assertSame(['type' => 'openrouter:files'], ChatOpenRouterServerTool::files()->toArray());
    }

    public function testRoundTripsThroughTheFactory(): void
    {
        $payload = ['type' => 'openrouter:fusion', 'parameters' => ['model' => 'openai/gpt-4o']];

        $this->assertSame($payload, ChatToolFactory::from($payload)->toArray());
    }
}
