<?php

namespace Phig\Tests\Parsers;

use Phig\Parsers\YamlParser;

class YamlParserTest extends ParserTestCase
{
    protected function getTestSubjectName()
    {
        return 'yaml';
    }

    protected function getTestSubject()
    {
        return new YamlParser();
    }
}
