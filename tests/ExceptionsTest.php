<?php

namespace Phig\Tests;

use Phig\ConfigLoader;
use Phig\Exceptions\ExtensionNotFoundException;
use Phig\Exceptions\FileNotFoundException;
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
