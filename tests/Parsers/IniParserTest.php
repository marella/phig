<?php

namespace Phig\Tests\Parsers;

use Phig\Parsers\IniParser;

class IniParserTest extends ParserTestCase
{
    protected function getTestSubjectName()
    {
        return 'ini';
    }

    protected function getTestSubject()
    {
        return new IniParser();
    }

    protected function parserAssertBooleans(array $data)
    {
        $this->assertSame('1', $data['key.true']);
        $this->assertSame('', $data['key.false']);
    }

    protected function parserAssertNull(array $data)
    {
        $this->assertSame('', $data['key.null']);
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
