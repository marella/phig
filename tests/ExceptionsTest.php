<?php

namespace Phig\Tests;

use Phig\ConfigLoader;
use Phig\Exceptions\ExtensionNotFoundException;
use Phig\Exceptions\FileNotFoundException;
use Phig\Exceptions\ParserException;
use Phig\Exceptions\UnsupportedExtensionException;
use PHPUnit_Framework_TestCase;
use UnexpectedValueException;

class ExceptionsTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->subject = $this->getTestSubject();
    }

    public function tearDown()
    {
        unset($this->subject);
    }

    public function testLoadNonExistent()
    {
        $this->setExpectedException(FileNotFoundException::class);
        $this->subject->load('non-existent');
    }

    public function testLoadNoExtension()
    {
        $this->setExpectedException(ExtensionNotFoundException::class);
        $this->subject->load(__DIR__.'/data/fail/noextension');
    }

    public function testLoadInvalidFormat()
    {
        $this->setExpectedException(UnexpectedValueException::class);
        $this->subject->load(__DIR__.'/data/fail/.invalidformat.php');
    }

    public function testLoadUnsupportedExtension()
    {
        $this->assertFalse($this->subject->hasParser('ext'));
        $this->setExpectedException(UnsupportedExtensionException::class);
        $this->subject->load(__DIR__.'/data/fail/unsupported.ext');
    }

    /**
     * @dataProvider failParsersPathProvider
     */
    public function testLoadThrowsParserException($path)
    {
        $this->setExpectedException(ParserException::class);
        $this->subject->load($path);
    }

    public function failParsersPathProvider()
    {
        $dir = __DIR__.'/data/parsers/fail';
        $paths = glob("$dir/*");
        $this->assertNotEmpty($paths);
        $this->assertTrue(is_array($paths));

        return array_map(function ($value) {
            return [$value];
        }, $paths);
    }

    public function testSetGetParserInvalidSet()
    {
        $this->assertFalse($this->subject->hasParser('ext'));
        $this->subject->setParser('ext', function () {
            return 'foo';
        });
        $this->assertTrue($this->subject->hasParser('ext'));
        $this->setExpectedException(UnexpectedValueException::class);
        $this->subject->getParser('ext');
    }

    public function testSetGetParserInvalidGet()
    {
        $this->assertFalse($this->subject->hasParser('ext'));
        $this->assertFalse($this->subject->hasParser('non-existent'));
        $this->subject->setParser('ext', function ($loader) {
            return $loader->getParser('non-existent');
        });
        $this->assertTrue($this->subject->hasParser('ext'));
        $this->setExpectedException(UnsupportedExtensionException::class);
        $this->subject->getParser('ext');
    }

    public function testSetGetParserRecursion()
    {
        $this->assertFalse($this->subject->hasParser('ext'));
        $this->subject->setParser('ext', function ($loader) {
            return $loader->getParser('ext');
        });
        $this->assertTrue($this->subject->hasParser('ext'));
        $this->setExpectedException(UnsupportedExtensionException::class);
        $this->subject->getParser('ext');
    }

    protected function getTestSubject()
    {
        return new ConfigLoader();
    }
}
