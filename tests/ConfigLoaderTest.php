<?php

namespace Phig\Tests;

use Phig\ConfigLoader;
use Phig\Parsers\IniParser;
use Phig\Parsers\JsonParser;
use Phig\Parsers\PhpParser;
use Phig\Parsers\XmlParser;
use Phig\Parsers\YamlParser;
use PHPUnit_Framework_TestCase;

class ConfigLoaderTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->subject = $this->getTestSubject();
    }

    public function tearDown()
    {
        unset($this->subject);
    }

    public function testLoadEmpty()
    {
        $config = $this->subject->load([]);
        $this->configAssertEmpty($config);

        $config = $this->subject->read([]);
        $this->configAssertEmpty($config);
    }

    public function testLoadEmptyDir()
    {
        $config = $this->subject->load(__DIR__.'/data/empty');
        $this->configAssertEmpty($config);

        $config = $this->subject->read(__DIR__.'/data/empty');
        $this->configAssertEmpty($config);
    }

    public function testLoadNonExistentOptional()
    {
        $config = $this->subject->load([], 'non-existent');
        $this->configAssertEmpty($config);

        $config = $this->subject->read([], 'non-existent');
        $this->configAssertEmpty($config);
    }

    public function testLoad()
    {
        $config = $this->subject->load(glob(__DIR__.'/data/pass/*'), [__DIR__.'/data/pass/.env.php']);
        $this->configAssertAll($config);

        $config = $this->subject->read(glob(__DIR__.'/data/pass/*'), [__DIR__.'/data/pass/.env.php']);
        $this->configAssertAll($config);
    }

    public function testLoadDir()
    {
        $config = $this->subject->load(__DIR__.'/data/pass', [__DIR__.'/data/pass/.env.php']);
        $this->configAssertAll($config);

        $config = $this->subject->read(__DIR__.'/data/pass', [__DIR__.'/data/pass/.env.php']);
        $this->configAssertAll($config);
    }

    public function testLoadFile()
    {
        $config = $this->subject->load(__DIR__.'/data/pass/a.php');
        $this->configAssertBasic($config);

        $config = $this->subject->read(__DIR__.'/data/pass/a.php');
        $this->configAssertBasic($config);
    }

    public function testSetParser()
    {
        $this->assertFalse($this->subject->hasParser('ext'));

        $this->subject->setParser('ext', function () {
            return new PhpParser();
        });

        $config = $this->subject->load(__DIR__.'/data/parsers/a.ext');
        $this->assertInstanceOf(PhpParser::class, $this->subject->getParser('ext'));
        $this->configAssertBasic($config);

        $config = $this->subject->read(__DIR__.'/data/parsers/a.ext');
        $this->configAssertBasic($config);
    }

    public function testSetParserUsingUse()
    {
        $this->assertFalse($this->subject->hasParser('ext'));

        $parser = new PhpParser();
        $this->subject->setParser('ext', function () use ($parser) {
            return $parser;
        });

        $config = $this->subject->load(__DIR__.'/data/parsers/a.ext');
        $this->assertInstanceOf(PhpParser::class, $this->subject->getParser('ext'));
        $this->configAssertBasic($config);

        $config = $this->subject->read(__DIR__.'/data/parsers/a.ext');
        $this->configAssertBasic($config);
    }

    public function testSetParserUsingLoader()
    {
        $this->assertFalse($this->subject->hasParser('ext'));

        $this->subject->setParser('ext', function ($loader) {
            return $loader->getParser('php');
        });

        $config = $this->subject->load(__DIR__.'/data/parsers/a.ext');
        $this->assertInstanceOf(PhpParser::class, $this->subject->getParser('ext'));
        $this->configAssertBasic($config);

        $config = $this->subject->read(__DIR__.'/data/parsers/a.ext');
        $this->configAssertBasic($config);
    }

    public function testHasParser()
    {
        $this->assertTrue($this->subject->hasParser('php'));

        $this->assertFalse($this->subject->hasParser('ext'));
        $this->subject->setParser('ext', function () {
            return new PhpParser();
        });
        $this->assertTrue($this->subject->hasParser('ext'));

        $this->assertFalse($this->subject->hasParser('ext2'));
        $this->subject->setParser('ext2', function () {
            return 'foo';
        });
        $this->assertTrue($this->subject->hasParser('ext2'));
    }

    public function testInbuiltParsers()
    {
        $this->assertInstanceOf(PhpParser::class, $this->subject->getParser('php'));
        $this->assertInstanceOf(JsonParser::class, $this->subject->getParser('json'));
        $this->assertInstanceOf(IniParser::class, $this->subject->getParser('ini'));
        $this->assertInstanceOf(XmlParser::class, $this->subject->getParser('xml'));
        $this->assertInstanceOf(YamlParser::class, $this->subject->getParser('yaml'));
        $this->assertSame($this->subject->getParser('yaml'), $this->subject->getParser('yml'));
    }

    protected function configAssertEmpty($config)
    {
        $this->assertSame([], $config->all());
    }

    protected function configAssertBasic($config)
    {
        $this->assertSame(1, $config['a.x.y']);
        $this->assertSame(2, $config['a.z']);
        $this->assertSame(['y' => 1], $config['a.x']);

        $this->assertSame($config['a.x.y'], $config['a']['x']['y']);
        $this->assertSame($config['a.z'], $config['a']['z']);
        $this->assertSame($config['a.x'], $config['a']['x']);

        $this->assertSame($config['a.x.y'], $config['a.x']['y']);
    }

    protected function configAssertPrefix($config)
    {
        $this->assertSame(3, $config['b.c.x.y']);
        $this->assertSame(4, $config['b.c.z']);
        $this->assertSame(['y' => 3], $config['b.c.x']);
    }

    protected function configAssertOverride($config)
    {
        $this->assertSame(7, $config['d.x.y']);
        $this->assertSame(8, $config['d.z']);
        $this->assertSame(['y' => 7], $config['d.x']);
        $this->assertSame('bar', $config['d.var']);
    }

    protected function configAssertAll($config)
    {
        $this->configAssertBasic($config);
        $this->configAssertPrefix($config);
        $this->configAssertOverride($config);
    }

    protected function getTestSubject()
    {
        return new ConfigLoader();
    }
}
