<?php

declare(strict_types=1);

namespace OpenRouter\Tests\Unit\ValueObjects\Chat;

use OpenRouter\ValueObjects\Chat\Config\GrammarResponseFormat;
use OpenRouter\ValueObjects\Chat\Config\JsonObjectResponseFormat;
use OpenRouter\ValueObjects\Chat\Config\JsonSchemaResponseFormat;
use OpenRouter\ValueObjects\Chat\Config\PythonResponseFormat;
use OpenRouter\ValueObjects\Chat\Config\ResponseFormatFactory;
use OpenRouter\ValueObjects\Chat\Config\TextResponseFormat;
use OpenRouter\ValueObjects\Chat\Config\UnknownResponseFormat;
use PHPUnit\Framework\TestCase;

final class ResponseFormatFactoryTest extends TestCase
{
    public function testTextDispatch(): void
    {
        $f = ResponseFormatFactory::from(['type' => 'text']);
        $this->assertInstanceOf(TextResponseFormat::class, $f);
        $this->assertSame(['type' => 'text'], $f->toArray());
    }

    public function testJsonObjectDispatch(): void
    {
        $f = ResponseFormatFactory::from(['type' => 'json_object']);
        $this->assertInstanceOf(JsonObjectResponseFormat::class, $f);
    }

    public function testJsonSchemaDispatchHydratesNestedConfig(): void
    {
        $f = ResponseFormatFactory::from([
            'type' => 'json_schema',
            'json_schema' => [
                'name' => 'math',
                'schema' => ['type' => 'object'],
                'strict' => true,
                'description' => 'desc',
            ],
        ]);

        $this->assertInstanceOf(JsonSchemaResponseFormat::class, $f);
        $this->assertSame('math', $f->name);
        $this->assertTrue($f->strict);
        $this->assertSame('desc', $f->description);
        $this->assertSame(['type' => 'object'], $f->schema);

        $array = $f->toArray();
        $this->assertSame('math', $array['json_schema']['name']);
        $this->assertSame(true, $array['json_schema']['strict']);
    }

    public function testGrammarDispatch(): void
    {
        $f = ResponseFormatFactory::from(['type' => 'grammar', 'grammar' => 'root ::= "yes"']);
        $this->assertInstanceOf(GrammarResponseFormat::class, $f);
        $this->assertSame('root ::= "yes"', $f->grammar);
    }

    public function testPythonDispatch(): void
    {
        $f = ResponseFormatFactory::from(['type' => 'python']);
        $this->assertInstanceOf(PythonResponseFormat::class, $f);
    }

    public function testUnknownTypeFallback(): void
    {
        $f = ResponseFormatFactory::from(['type' => 'novel_format', 'extra' => 'x']);
        $this->assertInstanceOf(UnknownResponseFormat::class, $f);
        $this->assertSame('novel_format', $f->type());
        $this->assertSame(['type' => 'novel_format', 'extra' => 'x'], $f->toArray());
    }
}
