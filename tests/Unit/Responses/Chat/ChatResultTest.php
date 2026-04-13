<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Responses\Chat;

use OpenRouter\Responses\Chat\ChatResult;
use OpenRouter\Responses\Meta\MetaInformation;
use OpenRouter\Tests\Fixtures\ChatCreateFixture;
use PHPUnit\Framework\TestCase;

final class ChatResultTest extends TestCase
{
    public function testFromHydratesFullFixture(): void
    {
        $result = ChatResult::from(ChatCreateFixture::ATTRIBUTES, MetaInformation::from([]));

        $this->assertSame('chatcmpl-abc123', $result->id);
        $this->assertSame(2, count($result->choices));
        $this->assertSame('The capital of France is Paris.', $result->choices[0]->message->content);
        $this->assertNotNull($result->usage);
        $this->assertSame(25, $result->usage->totalTokens);
    }

    public function testToArrayRoundTripsKnownFields(): void
    {
        $result = ChatResult::from(ChatCreateFixture::ATTRIBUTES, MetaInformation::from([]));

        $array = $result->toArray();
        $this->assertSame('chatcmpl-abc123', $array['id']);
        $this->assertSame('chat.completion', $array['object']);
        $this->assertSame(10, $array['usage']['prompt_tokens']);
        $this->assertSame(0.0012, $array['usage']['cost']);
    }

    public function testHydratesMissingOptionalFieldsAsNull(): void
    {
        $minimal = [
            'id' => 'cc-1',
            'object' => 'chat.completion',
            'created' => 1,
            'model' => 'm',
            'choices' => [],
        ];

        $result = ChatResult::from($minimal, MetaInformation::from([]));

        $this->assertSame([], $result->choices);
        $this->assertNull($result->usage);
        $this->assertNull($result->systemFingerprint);
        $this->assertNull($result->serviceTier);
        $this->assertNull($result->provider);
    }

    public function testPreservesUnknownTopLevelFieldsInExtras(): void
    {
        $payload = ChatCreateFixture::ATTRIBUTES;
        $payload['x_openrouter_id'] = 'gen-xyz';

        $result = ChatResult::from($payload, MetaInformation::from([]));

        $this->assertArrayHasKey('x_openrouter_id', $result->extras);
        $this->assertSame('gen-xyz', $result->extras['x_openrouter_id']);
        $this->assertSame('gen-xyz', $result->toArray()['x_openrouter_id']);
    }
}
