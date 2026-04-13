<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\Responses\Responses;

use OpenRouter\Responses\Responses\CreateResponseOutputAnnotation;
use OpenRouter\Responses\Responses\CreateResponseOutputContent;
use OpenRouter\Responses\Responses\LogProbs;
use OpenRouter\Responses\Responses\TopLogprobs;
use PHPUnit\Framework\TestCase;

final class CreateResponseOutputContentTest extends TestCase
{
    public function testOutputTextWithTypedAnnotations(): void
    {
        $content = CreateResponseOutputContent::from([
            'type' => 'output_text',
            'text' => 'See the docs.',
            'annotations' => [
                [
                    'type' => 'url_citation',
                    'start_index' => 4,
                    'end_index' => 12,
                    'url' => 'https://example.com/docs',
                    'title' => 'Docs',
                ],
            ],
        ]);

        $this->assertCount(1, $content->annotations);
        $annotation = $content->annotations[0];
        $this->assertInstanceOf(CreateResponseOutputAnnotation::class, $annotation);
        $this->assertSame('url_citation', $annotation->type);
        $this->assertSame(4, $annotation->startIndex);
        $this->assertSame(12, $annotation->endIndex);
        $this->assertSame('https://example.com/docs', $annotation->url);
        $this->assertSame('Docs', $annotation->title);

        $arr = $content->toArray();
        $this->assertSame('output_text', $arr['type']);
        $this->assertSame('See the docs.', $arr['text']);
        $this->assertSame([
            [
                'type' => 'url_citation',
                'start_index' => 4,
                'end_index' => 12,
                'url' => 'https://example.com/docs',
                'title' => 'Docs',
            ],
        ], $arr['annotations']);
    }

    public function testRefusalContentExposesTypedFields(): void
    {
        $content = CreateResponseOutputContent::from([
            'type' => 'refusal',
            'refusal' => "I can't help with that.",
            'refusal_reason' => 'safety',
        ]);

        $this->assertSame('refusal', $content->type);
        $this->assertNull($content->text);
        $this->assertSame("I can't help with that.", $content->refusal);
        $this->assertSame('safety', $content->refusalReason);

        $arr = $content->toArray();
        $this->assertSame("I can't help with that.", $arr['refusal']);
        $this->assertSame('safety', $arr['refusal_reason']);
        $this->assertArrayNotHasKey('text', $arr);
    }

    public function testLogprobsHydratedAsTypedList(): void
    {
        $content = CreateResponseOutputContent::from([
            'type' => 'output_text',
            'text' => 'Hi',
            'logprobs' => [
                [
                    'token' => 'Hi',
                    'logprob' => -0.05,
                    'bytes' => [72, 105],
                    'top_logprobs' => [
                        ['token' => 'Hello', 'logprob' => -0.10, 'bytes' => [72]],
                    ],
                ],
            ],
        ]);

        $this->assertCount(1, $content->logprobs);
        $first = $content->logprobs[0];
        $this->assertInstanceOf(LogProbs::class, $first);
        $this->assertSame('Hi', $first->token);
        $this->assertSame(-0.05, $first->logprob);
        $this->assertSame([72, 105], $first->bytes);
        $this->assertCount(1, $first->topLogprobs);
        $this->assertInstanceOf(TopLogprobs::class, $first->topLogprobs[0]);
        $this->assertSame('Hello', $first->topLogprobs[0]->token);

        $arr = $content->toArray();
        $this->assertSame('Hi', $arr['logprobs'][0]['token']);
        $this->assertSame([72, 105], $arr['logprobs'][0]['bytes']);
        $this->assertSame('Hello', $arr['logprobs'][0]['top_logprobs'][0]['token']);
    }

    public function testLogprobsMissingRemainsEmpty(): void
    {
        $content = CreateResponseOutputContent::from(['type' => 'output_text', 'text' => 'x']);

        $this->assertSame([], $content->logprobs);
        $this->assertArrayNotHasKey('logprobs', $content->toArray());
    }

    public function testUnknownAnnotationTypePreservedThroughExtra(): void
    {
        $annotation = CreateResponseOutputAnnotation::from([
            'type' => 'file_path_citation',
            'file_id' => 'file-1',
            'index' => 7,
        ]);

        $this->assertSame('file_path_citation', $annotation->type);
        $this->assertSame(['file_id' => 'file-1', 'index' => 7], $annotation->extra);
        $this->assertSame([
            'type' => 'file_path_citation',
            'file_id' => 'file-1',
            'index' => 7,
        ], $annotation->toArray());
    }
}
