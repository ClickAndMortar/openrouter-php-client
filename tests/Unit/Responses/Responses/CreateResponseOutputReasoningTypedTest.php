<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Responses\Responses;

use OpenRouter\Responses\Responses\CreateResponseOutputReasoning;
use OpenRouter\ValueObjects\Responses\Input\Content\ReasoningContentItem;
use OpenRouter\ValueObjects\Responses\Input\Content\ReasoningSummaryItem;
use PHPUnit\Framework\TestCase;

final class CreateResponseOutputReasoningTypedTest extends TestCase
{
    public function testHydratesContentAndSummaryToTypedItems(): void
    {
        $reasoning = CreateResponseOutputReasoning::from([
            'id' => 'rs-1',
            'type' => 'reasoning',
            'status' => 'completed',
            'summary' => [
                ['type' => 'summary_text', 'text' => 'Quick plan'],
            ],
            'content' => [
                ['type' => 'reasoning.text', 'text' => 'Let me think...'],
                ['type' => 'reasoning.encrypted', 'data' => 'opaque', 'format' => 'masked'],
            ],
        ]);

        $this->assertCount(1, $reasoning->summary);
        $this->assertInstanceOf(ReasoningSummaryItem::class, $reasoning->summary[0]);
        $this->assertSame('Quick plan', $reasoning->summary[0]->text);

        $this->assertCount(2, $reasoning->content);
        $this->assertInstanceOf(ReasoningContentItem::class, $reasoning->content[0]);
        $this->assertSame(ReasoningContentItem::TYPE_TEXT, $reasoning->content[0]->type);
        $this->assertSame('Let me think...', $reasoning->content[0]->text);

        $this->assertSame(ReasoningContentItem::TYPE_ENCRYPTED, $reasoning->content[1]->type);
        $this->assertSame('opaque', $reasoning->content[1]->data);
        $this->assertSame('masked', $reasoning->content[1]->format);
    }

    public function testRoundTripsBackToRawArray(): void
    {
        $payload = [
            'id' => 'rs-2',
            'type' => 'reasoning',
            'summary' => [['type' => 'summary_text', 'text' => 'Plan']],
            'content' => [['type' => 'reasoning.text', 'text' => 'Body']],
        ];

        $reasoning = CreateResponseOutputReasoning::from($payload);
        $this->assertSame($payload['summary'], $reasoning->toArray()['summary']);
        $this->assertSame($payload['content'], $reasoning->toArray()['content']);
    }
}
