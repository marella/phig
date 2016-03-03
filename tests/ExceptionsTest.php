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
        $this->setExpectedException(UnsupportedExtensionException::class);
        $this->subject->load(__DIR__.'/data/fail/unsupported.ext');
    }

    public function testSetGetParser()
    {
        $this->subject->setParser('ext', function () {
            return 'foo';
        });
        $this->setExpectedException(UnexpectedValueException::class);
        $this->subject->getParser('ext');
    }

    protected function getTestSubject()
    {
        return new ConfigLoader();
    }
}
