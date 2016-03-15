<?php

namespace Phig\Tests\Parsers;

use Phig\Parsers\PhpParser;

class PhpParserTest extends ParserTestCase
{
    protected function getTestSubjectName()
    {
        return 'php';
    }

    protected function getTestSubject()
    {
        return new PhpParser();
    }
}
