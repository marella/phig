<?php

namespace Phig\Tests\Parsers;

use Phig\Contracts\ParserInterface;
use Phig\Exceptions\ParserException;
use PHPUnit_Framework_Error_Notice;
use PHPUnit_Framework_Error_Warning;
use PHPUnit_Framework_TestCase;

abstract class ParserTestCase extends PHPUnit_Framework_TestCase
{
    abstract protected function getTestSubjectName();

    abstract protected function getTestSubject();

    protected function getDataDir()
    {
        return __DIR__.'/../data/parsers';
    }

    public function setUp()
    {
        $this->subject = $this->getTestSubject();
        $this->assertInstanceOf(ParserInterface::class, $this->subject);
    }

    public function tearDown()
    {
        unset($this->subject);
    }

    /**
     * @dataProvider pathProvider
     */
    public function testParse($path)
    {
        $data = $this->subject->parse($path);
        $this->assertInternalType('array', $data);

        $this->parserAssertAll($data);
    }

    protected function parserAssertAll(array $data)
    {
        $this->parserAssertBooleans($data);
        $this->parserAssertNull($data);
        $this->parserAssertInt($data);
        $this->parserAssertFloat($data);
        $this->parserAssertString($data);
        $this->parserAssertArray($data);
    }

    protected function parserAssertBooleans(array $data)
    {
        $this->assertTrue($data['true']);
        $this->assertFalse($data['false']);
    }

    protected function parserAssertNull(array $data)
    {
        $this->assertNull($data['null']);
    }

    protected function parserAssertInt(array $data)
    {
        $this->assertSame(1, $data['int']);
    }

    protected function parserAssertFloat(array $data)
    {
        $this->assertSame(2.3, $data['float']);
    }

    protected function parserAssertString(array $data)
    {
        $this->assertSame('foo', $data['string']);
    }

    protected function parserAssertArray(array $data)
    {
        $this->assertSame(['bar', 'baz'], $data['array']);
        $this->assertSame(['array' => ['bar', 'baz']], $data['nested']);
    }

    public function pathProvider()
    {
        $dir = $this->getDataDir().'/pass';
        $name = $this->getTestSubjectName();
        $paths = ["a.$name"/*, "$name.ext", $name*/];

        return array_map(function ($value) use ($dir) {
            return ["$dir/$value"];
        }, $paths);
    }

    /**
     * @dataProvider failPathProvider
     */
    public function testParseThrowsException($path)
    {
        $name = $this->getTestSubjectName();

        // don't trigger phpunit exceptions when parsing files
        // as exceptions should also be thrown outside phpunit framework
        PHPUnit_Framework_Error_Warning::$enabled = false;
        PHPUnit_Framework_Error_Notice::$enabled = false;
        try {
            $this->subject->parse($path);
        } catch (ParserException $e) {
        }
        // re-enable them after parsing is done
        PHPUnit_Framework_Error_Warning::$enabled = true;
        PHPUnit_Framework_Error_Notice::$enabled = true;

        $this->assertInstanceOf(ParserException::class, $e);

        $expected = constant(ParserException::class.'::'.strtoupper($name));
        $actual = $e->getCode();
        $this->assertSame($expected, $actual);

        // make sure phpunit exceptions are re-enabled
        $this->setExpectedException(PHPUnit_Framework_Error_Notice::class);
        ++$undefined;
    }

    public function failPathProvider()
    {
        $dir = $this->getDataDir().'/fail';
        $name = $this->getTestSubjectName();
        $paths = glob("$dir/*.$name");

        return array_map(function ($value) {
            return [$value];
        }, $paths);
    }
}
