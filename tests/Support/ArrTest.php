<?php

namespace Phig\Tests\Support;

use ArrayObject;
use Phig\Exceptions\KeyNotFoundException;
use Phig\Support\Arr;
use PHPUnit_Framework_TestCase;
use stdClass;

class ArrTest extends PHPUnit_Framework_TestCase
{
    public function testIs()
    {
        $this->assertTrue(Arr::accessible([]));
        $this->assertTrue(Arr::accessible([1, 2]));
        $this->assertTrue(Arr::accessible(['a' => 1, 'b' => 2]));

        $this->assertFalse(Arr::accessible(null));
        $this->assertFalse(Arr::accessible('abc'));
        $this->assertFalse(Arr::accessible(new stdClass()));
        $this->assertFalse(Arr::accessible((object) ['a' => 1, 'b' => 2]));
    }

    public function testExists()
    {
        $this->assertTrue(Arr::exists([1], 0));
        $this->assertTrue(Arr::exists([null], 0));
        $this->assertTrue(Arr::exists(['a' => 1], 'a'));
        $this->assertTrue(Arr::exists(['a' => null], 'a'));

        $this->assertFalse(Arr::exists([1], 1));
        $this->assertFalse(Arr::exists([null], 1));
        $this->assertFalse(Arr::exists(['a' => 1], 0));
    }

    public function testDot()
    {
        $array = Arr::dot(['foo' => ['bar' => 'baz']]);
        $this->assertEquals(['foo.bar' => 'baz'], $array);
    }

    public function testGet()
    {
        $array = ['products' => ['desk' => ['price' => 100]]];
        $value = Arr::get($array, null);
        $this->assertSame($array, $value);

        $array = ['products' => ['desk' => ['price' => 100]]];
        $value = Arr::get($array, 'products.desk');
        $this->assertEquals(['price' => 100], $value);

        // Test null array values
        $array = ['foo' => null, 'bar' => ['baz' => null]];
        $this->assertNull(Arr::get($array, 'foo', 'default'));
        $this->assertNull(Arr::get($array, 'bar.baz', 'default'));

        // Test direct ArrayAccess object
        $array = ['products' => ['desk' => ['price' => 100]]];
        $arrayAccessObject = new ArrayObject($array);
        $value = Arr::get($arrayAccessObject, 'products.desk');
        $this->assertEquals(['price' => 100], $value);

        // Test array containing ArrayAccess object
        $arrayAccessChild = new ArrayObject(['products' => ['desk' => ['price' => 100]]]);
        $array = ['child' => $arrayAccessChild];
        $value = Arr::get($array, 'child.products.desk');
        $this->assertEquals(['price' => 100], $value);

        // Test array containing multiple nested ArrayAccess objects
        $arrayAccessChild = new ArrayObject(['products' => ['desk' => ['price' => 100]]]);
        $arrayAccessParent = new ArrayObject(['child' => $arrayAccessChild]);
        $array = ['parent' => $arrayAccessParent];
        $value = Arr::get($array, 'parent.child.products.desk');
        $this->assertEquals(['price' => 100], $value);

        // Test missing ArrayAccess object field
        $arrayAccessChild = new ArrayObject(['products' => ['desk' => ['price' => 100]]]);
        $arrayAccessParent = new ArrayObject(['child' => $arrayAccessChild]);
        $array = ['parent' => $arrayAccessParent];
        $value = Arr::get($array, 'parent.child.desk', 'default');
        $this->assertSame('default', $value);

        // Test missing ArrayAccess object field
        $arrayAccessObject = new ArrayObject(['products' => ['desk' => null]]);
        $array = ['parent' => $arrayAccessObject];
        $value = Arr::get($array, 'parent.products.desk.price', 'default');
        $this->assertSame('default', $value);

        // Test null ArrayAccess object fields
        $array = new ArrayObject(['foo' => null, 'bar' => new ArrayObject(['baz' => null])]);
        $this->assertNull(Arr::get($array, 'foo', 'default'));
        $this->assertNull(Arr::get($array, 'bar.baz', 'default'));
    }

    public function testGetThrowsException($value = '')
    {
        // Test missing ArrayAccess object field
        $arrayAccessChild = new ArrayObject(['products' => ['desk' => ['price' => 100]]]);
        $arrayAccessParent = new ArrayObject(['child' => $arrayAccessChild]);
        $array = ['parent' => $arrayAccessParent];
        $this->setExpectedException(KeyNotFoundException::class);
        $value = Arr::get($array, 'parent.child.desk');
    }

    public function testHas()
    {
        $array = ['products' => ['desk' => ['price' => 100]]];
        $this->assertTrue(Arr::has($array, 'products.desk'));
        $this->assertTrue(Arr::has($array, 'products.desk.price'));
        $this->assertFalse(Arr::has($array, 'products.foo'));
        $this->assertFalse(Arr::has($array, 'products.desk.foo'));

        $array = ['foo' => null, 'bar' => ['baz' => null]];
        $this->assertTrue(Arr::has($array, 'foo'));
        $this->assertTrue(Arr::has($array, 'bar.baz'));

        $array = new ArrayObject(['foo' => 10, 'bar' => new ArrayObject(['baz' => 10])]);
        $this->assertTrue(Arr::has($array, 'foo'));
        $this->assertTrue(Arr::has($array, 'bar'));
        $this->assertTrue(Arr::has($array, 'bar.baz'));
        $this->assertFalse(Arr::has($array, 'xxx'));
        $this->assertFalse(Arr::has($array, 'xxx.yyy'));
        $this->assertFalse(Arr::has($array, 'foo.xxx'));
        $this->assertFalse(Arr::has($array, 'bar.xxx'));

        $array = new ArrayObject(['foo' => null, 'bar' => new ArrayObject(['baz' => null])]);
        $this->assertTrue(Arr::has($array, 'foo'));
        $this->assertTrue(Arr::has($array, 'bar.baz'));
    }

    public function testSet()
    {
        $array = ['products' => ['desk' => ['price' => 100]]];
        Arr::set($array, null, 200);
        $this->assertSame(200, $array);

        $array = ['products' => ['desk' => ['price' => 100]]];
        Arr::set($array, 'products.desk.price', 200);
        $this->assertEquals(['products' => ['desk' => ['price' => 200]]], $array);
    }
}
