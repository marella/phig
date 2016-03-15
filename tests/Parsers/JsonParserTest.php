<?php

namespace Phig\Tests\Parsers;

use Phig\Parsers\JsonParser;

class JsonParserTest extends ParserTestCase
{
    protected function getTestSubjectName()
    {
        return 'json';
    }

    protected function getTestSubject()
    {
        return new JsonParser();
    }
}
