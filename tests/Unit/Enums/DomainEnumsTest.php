<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Enums;

use OpenRouter\Enums\Messages\MessagesStopReason;
use OpenRouter\Enums\Responses\MessageRole;
use OpenRouter\Enums\Responses\OutputModality;
use OpenRouter\Enums\Responses\ReasoningEffort;
use OpenRouter\Enums\Responses\ReasoningSummaryVerbosity;
use OpenRouter\Enums\Responses\ResponseStatus;
use OpenRouter\Enums\Responses\ServiceTier;
use OpenRouter\Enums\Responses\ToolCallStatus;
use OpenRouter\Enums\Responses\Tools\ComputerEnvironment;
use OpenRouter\Enums\Responses\Tools\ImageQuality;
use OpenRouter\Enums\Responses\Tools\SearchContextSize;
use OpenRouter\Enums\Responses\Tools\WebSearchEngine;
use PHPUnit\Framework\TestCase;

final class DomainEnumsTest extends TestCase
{
    public function testServiceTierValues(): void
    {
        $this->assertSame(['auto', 'default', 'fast', 'flex', 'priority', 'scale'], ServiceTier::values());
        $this->assertSame('default', ServiceTier::Default_->value);
        $this->assertSame('fast', ServiceTier::Fast->value);
    }

    public function testReasoningEffortValues(): void
    {
        $this->assertSame(['max', 'xhigh', 'high', 'medium', 'low', 'minimal', 'none'], ReasoningEffort::values());
    }

    public function testReasoningSummaryVerbosityValues(): void
    {
        $this->assertSame(['auto', 'concise', 'detailed'], ReasoningSummaryVerbosity::values());
    }

    public function testOutputModalityValues(): void
    {
        $this->assertSame(
            ['text', 'image', 'embeddings', 'audio', 'video', 'rerank'],
            OutputModality::values(),
        );
    }

    public function testResponseStatusValues(): void
    {
        $this->assertSame(
            ['completed', 'incomplete', 'in_progress', 'failed', 'cancelled', 'queued'],
            ResponseStatus::values(),
        );
    }

    public function testMessageRoleValues(): void
    {
        $this->assertSame(
            ['user', 'assistant', 'system', 'developer', 'tool'],
            MessageRole::values(),
        );
    }

    public function testToolCallStatusValues(): void
    {
        $this->assertSame(
            ['in_progress', 'interpreting', 'completed', 'incomplete'],
            ToolCallStatus::values(),
        );
    }

    public function testWebSearchEngineValues(): void
    {
        $this->assertSame(
            ['auto', 'native', 'exa', 'firecrawl', 'parallel'],
            WebSearchEngine::values(),
        );
    }

    public function testSearchContextSizeValues(): void
    {
        $this->assertSame(['low', 'medium', 'high'], SearchContextSize::values());
    }

    public function testComputerEnvironmentValues(): void
    {
        $this->assertSame(
            ['windows', 'mac', 'linux', 'ubuntu', 'browser'],
            ComputerEnvironment::values(),
        );
    }

    public function testImageQualityValues(): void
    {
        $this->assertSame(['low', 'medium', 'high', 'auto'], ImageQuality::values());
    }

    public function testMessagesStopReasonValues(): void
    {
        $this->assertSame(
            [
                'end_turn',
                'max_tokens',
                'model_context_window_exceeded',
                'stop_sequence',
                'tool_use',
                'pause_turn',
                'refusal',
                'compaction',
            ],
            MessagesStopReason::values(),
        );
    }

    public function testTryFromUnknownValue(): void
    {
        $this->assertNull(WebSearchEngine::tryFrom('made-up'));
        $this->assertSame(WebSearchEngine::Exa, WebSearchEngine::tryFrom('exa'));
    }
}
