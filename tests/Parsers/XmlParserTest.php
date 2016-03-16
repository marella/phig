<?php

namespace Phig\Tests\Parsers;

use Phig\Parsers\XmlParser;

class XmlParserTest extends ParserTestCase
{
    protected function getTestSubjectName()
    {
        return 'xml';
    }

    protected function getTestSubject()
    {
        return new XmlParser();
    }

    protected function parserAssertBooleans(array $data)
    {
        $this->assertSame('true', $data['key.true']);
        $this->assertSame('false', $data['key.false']);
    }

    protected function parserAssertNull(array $data)
    {
        $this->assertSame('null', $data['key.null']);
    }

    protected function parserAssertInt(array $data)
    {
        $this->assertSame('1', $data['key.int']);
    }

    protected function parserAssertFloat(array $data)
    {
        $this->assertSame('2.3', $data['key.float']);
    }
}
