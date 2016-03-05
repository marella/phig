<?php

namespace Phig\Tests;

use BadMethodCallException;
use InvalidArgumentException;
use Phig\Config;
use Phig\Contracts\ConfigInterface;
use Phig\Exceptions\KeyNotFoundException;
use Phig\ReadConfig;
use PHPUnit_Framework_TestCase;
use UnexpectedValueException;

class ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider configProvider
     */
    public function testHas(ConfigInterface $config)
    {
        $this->assertTrue($config->has('x.y'));
        $this->assertTrue($config->has('z'));
        $this->assertTrue($config->has('x'));
        $this->assertTrue($config->has('foo'));
        $this->assertTrue($config->has('empty'));

        $this->assertFalse($config->has('x.z'));
        $this->assertFalse($config->has('x.y.z'));
        $this->assertFalse($config->has('z.x'));
        $this->assertFalse($config->has('y'));
        $this->assertFalse($config->has('foo.bar'));
        $this->assertFalse($config->has('non-existent'));
        $this->assertFalse($config->has(null));

        // test array access

        $this->assertTrue(isset($config['x.y']));
        $this->assertTrue(isset($config['x']['y']));
        $this->assertTrue(isset($config['z']));
        $this->assertTrue(isset($config['x']));
        $this->assertTrue(isset($config['foo']));
        $this->assertTrue(isset($config['empty']));

        $this->assertFalse(isset($config['x.z']));
        $this->assertFalse(isset($config['x']['z']));
        $this->assertFalse(isset($config['x.y.z']));
        $this->assertFalse(isset($config['x']['y']['z']));
        $this->assertFalse(isset($config['z.x']));
        $this->assertFalse(isset($config['z']['x']));
        $this->assertFalse(isset($config['y']));
        $this->assertFalse(isset($config['foo.bar']));
        $this->assertFalse(isset($config['foo']['bar']));
        $this->assertFalse(isset($config['non-existent']));
        $this->assertFalse(isset($config[null]));
    }

    /**
     * @dataProvider configProvider
     */
    public function testGet(ConfigInterface $config)
    {
        $this->assertSame(1, $config->get('x.y'));
        $this->assertSame(2, $config->get('z'));
        $this->assertSame(['y' => 1], $config->get('x'));
        $this->assertSame('bar', $config->get('foo'));
        $this->assertNull($config->get('empty'));
        $this->assertNull($config->get('empty', 'default'));

        $this->assertSame($config->get('x.y'), $config->get('x')['y']);

        // test default value

        $this->assertFalse($config->has('non-existent'));
        $this->assertSame('default', $config->get('non-existent', 'default'));
        $this->assertTrue($config->has('x'));
        $this->assertFalse($config->has('x.non-existent'));
        $this->assertSame('default', $config->get('x.non-existent', 'default'));

        // test ArrayAccess

        $this->assertSame(1, $config['x.y']);
        $this->assertSame(2, $config['z']);
        $this->assertSame(['y' => 1], $config['x']);
        $this->assertSame('bar', $config['foo']);
        $this->assertNull($config['empty']);

        $this->assertSame($config['x.y'], $config['x']['y']);
    }

    /**
     * @dataProvider configProvider
     */
    public function testGetNonExistentThrowsException(ConfigInterface $config)
    {
        $this->assertFalse($config->has('non-existent'));
        $this->setExpectedException(KeyNotFoundException::class);
        $config->get('non-existent');
    }

    /**
     * @dataProvider configProvider
     */
    public function testGetNullThrowsException(ConfigInterface $config)
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $config->get(null);
    }

    public function configProvider()
    {
        $array = $this->arrayProvider();
        $config = new Config($array);
        $read = new ReadConfig($array);

        return [
            [$config],
            [$read],
            [$read], // pass same read config twice to check cache
        ];
    }

    /* Write Config tests */

    /**
     * @dataProvider writeConfigProvider
     */
    public function testSet(ConfigInterface $config)
    {
        $this->assertFalse($config->has('key'));
        $config->set('key', 'value');
        $this->assertTrue($config->has('key'));
        $this->assertSame('value', $config->get('key'));

        $this->assertFalse($config->has('key2'));
        $config['key2'] = 'value2';
        $this->assertTrue($config->has('key2'));
        $this->assertSame('value2', $config->get('key2'));

        $this->assertFalse($config->has('key3'));
        $this->assertFalse($config->has('key4'));
        $config->set(['key3' => 'value3', 'key4' => 'value4']);
        $this->assertSame('value3', $config->get('key3'));
        $this->assertSame('value4', $config->get('key4'));

        $this->assertSame(1, $config['x']['y']);
        $config->set('x.y', 2);
        $this->assertSame(2, $config['x']['y']);
        $config['x.y'] = 3;
        $this->assertSame(3, $config['x']['y']);
    }

    /**
     * @dataProvider writeConfigProvider
     */
    public function testSetNullThrowsException(ConfigInterface $config)
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $config->set(null, 'value');
    }

    /**
     * @dataProvider writeConfigProvider
     */
    public function testUnset(ConfigInterface $config)
    {
        $this->assertNotNull($config->get('z'));
        unset($config['z']);
        $this->assertNull($config->get('z'));

        $this->assertNotNull($config->get('x.y'));
        unset($config['x.y']);
        $this->assertNull($config->get('x.y'));

        $this->assertNotNull($config->get('x'));
        unset($config['x']);
        $this->assertNull($config->get('x'));
        $this->assertFalse($config->has('x.y'));
    }

    /**
     * @dataProvider writeConfigProvider
     */
    public function testPrepend(ConfigInterface $config)
    {
        $this->assertSame(['y' => 1], $config['x']);
        $config->prepend('x', 'value');
        $this->assertSame(['value', 'y' => 1], $config['x']);
    }

    /**
     * @dataProvider writeConfigProvider
     */
    public function testPush(ConfigInterface $config)
    {
        $this->assertSame(['y' => 1], $config['x']);
        $config->push('x', 'value');
        $this->assertSame(['y' => 1, 'value'], $config['x']);
    }

    /**
     * @dataProvider writeConfigProvider
     */
    public function testPrependNonExistentThrowsException(ConfigInterface $config)
    {
        $this->assertFalse($config->has('non-existent'));
        $this->setExpectedException(KeyNotFoundException::class);
        $config->prepend('non-existent', 'value');
    }

    /**
     * @dataProvider writeConfigProvider
     */
    public function testPrependNonArrayThrowsException(ConfigInterface $config)
    {
        $this->assertNotInternalType('array', $config['z']);
        $this->setExpectedException(UnexpectedValueException::class);
        $config->prepend('z', 'value');
    }

    /**
     * @dataProvider writeConfigProvider
     */
    public function testPushNonExistentThrowsException(ConfigInterface $config)
    {
        $this->assertFalse($config->has('non-existent'));
        $this->setExpectedException(KeyNotFoundException::class);
        $config->push('non-existent', 'value');
    }

    /**
     * @dataProvider writeConfigProvider
     */
    public function testPushNonArrayThrowsException(ConfigInterface $config)
    {
        $this->assertNotInternalType('array', $config['z']);
        $this->setExpectedException(UnexpectedValueException::class);
        $config->push('z', 'value');
    }

    public function writeConfigProvider()
    {
        $array = $this->arrayProvider();
        $config = new Config($array);

        return [
            [$config],
        ];
    }

    /* Read Config tests */

    /**
     * @dataProvider readConfigProvider
     */
    public function testReadConfigSetThrowsException(ConfigInterface $config)
    {
        $this->setExpectedException(BadMethodCallException::class);
        $config->set('key', 'value');
    }

    /**
     * @dataProvider readConfigProvider
     */
    public function testReadConfigSetArrayAccessThrowsException(ConfigInterface $config)
    {
        $this->setExpectedException(BadMethodCallException::class);
        $config['key'] = 'value';
    }

    /**
     * @dataProvider readConfigProvider
     */
    public function testReadConfigUnsetThrowsException(ConfigInterface $config)
    {
        $this->setExpectedException(BadMethodCallException::class);
        unset($config['x']);
    }

    /**
     * @dataProvider readConfigProvider
     */
    public function testReadConfigPrependThrowsException(ConfigInterface $config)
    {
        $this->setExpectedException(BadMethodCallException::class);
        $config->prepend('x', 'value');
    }

    /**
     * @dataProvider readConfigProvider
     */
    public function testReadConfigPushThrowsException(ConfigInterface $config)
    {
        $this->setExpectedException(BadMethodCallException::class);
        $config->push('x', 'value');
    }

    public function readConfigProvider()
    {
        $array = $this->arrayProvider();
        $read = new ReadConfig($array);

        return [
            [$read],
        ];
    }

    public function arrayProvider()
    {
        return [
            'x' => [
                'y' => 1,
            ],
            'z' => 2,
            'foo' => 'bar',
            'empty' => null,
        ];
    }
}
