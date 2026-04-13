<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\ValueObjects\Responses\Input;

use OpenRouter\Exceptions\InvalidArgumentException;
use OpenRouter\ValueObjects\Responses\Input\Content\InputAudioPart;
use OpenRouter\ValueObjects\Responses\Input\Content\InputFilePart;
use OpenRouter\ValueObjects\Responses\Input\Content\InputImagePart;
use OpenRouter\ValueObjects\Responses\Input\Content\InputTextPart;
use OpenRouter\ValueObjects\Responses\Input\Content\InputVideoPart;
use OpenRouter\ValueObjects\Responses\Input\InputFunctionCallOutput;
use PHPUnit\Framework\TestCase;

final class InputFunctionCallOutputTest extends TestCase
{
    public function testStringOutputSerializes(): void
    {
        $vo = new InputFunctionCallOutput(
            callId: 'call-abc',
            output: '{"temperature":72}',
            id: 'output-1',
            status: 'completed',
        );

        $this->assertSame([
            'type' => 'function_call_output',
            'call_id' => 'call-abc',
            'output' => '{"temperature":72}',
            'id' => 'output-1',
            'status' => 'completed',
        ], $vo->toArray());
    }

    public function testTextImageAndFilePartsAreAccepted(): void
    {
        $vo = new InputFunctionCallOutput(
            callId: 'call-abc',
            output: [
                new InputTextPart('result summary'),
                new InputImagePart(imageUrl: 'https://example.com/chart.png'),
                new InputFilePart(fileUrl: 'https://example.com/report.pdf'),
            ],
        );

        $result = $vo->toArray();
        $this->assertCount(3, $result['output']);
        $this->assertSame('input_text', $result['output'][0]['type']);
        $this->assertSame('input_image', $result['output'][1]['type']);
        $this->assertSame('input_file', $result['output'][2]['type']);
    }

    public function testAudioPartIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('FunctionCallOutputContentPart');

        /** @phpstan-ignore-next-line intentional bad input */
        new InputFunctionCallOutput(
            callId: 'call-abc',
            output: [new InputAudioPart(data: 'abc', format: 'mp3')],
        );
    }

    public function testVideoPartIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);

        /** @phpstan-ignore-next-line intentional bad input */
        new InputFunctionCallOutput(
            callId: 'call-abc',
            output: [new InputVideoPart(videoUrl: 'https://example.com/v.mp4')],
        );
    }
}
