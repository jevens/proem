<?php

/**
 * The MIT License
 *
 * Copyright (c) 2010 - 2012 Tony R Quilkey <trq@proemframework.org>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Proem\Tests;

use Proem\Service\Asset\Generic as Asset,
    Proem\Service\Manager,
    Proem\Service\Asset\Foo,
    Proem\Service\Asset\Bar;

class ServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testCanInstantiateAsset()
    {
        $a = new Asset;
        $this->assertInstanceOf('Proem\Service\Asset\Generic', $a);
    }

    public function testCanInstantiateAssetManager()
    {
        $am = new Manager;
        $this->assertInstanceOf('Proem\Service\Manager', $am);
    }

    public function testAssetCanInstantiate()
    {
        $bar = new Asset;
        $bar->set('Proem\Service\Asset\Bar', function() {
            return new Bar;
        });

        $this->assertInstanceOf('Proem\Service\Asset\Bar', $bar->get());
    }

    public function testAssetCanSetParams()
    {
        $foo = new Asset;
        $foo->setParam('name', 'trq')
            ->set('\Proem\Service\Asset\Foo', function($a) {
                return new Foo($a->getParam('name'));
            });

        $asset = $foo->get();

        $this->assertEquals('Hello trq', $asset->say());
    }

    public function testMagicGetSetParams()
    {
        $foo = new Asset;
        $foo->name = 'trq';
        $this->assertEquals('trq', $foo->name);
    }

    public function testAssetCanSetMultipleParams()
    {
        $foo = new Asset;
        $foo->setParams([
            'foo' => 'bar',
            'boo' => 'bob'
        ]);
        $this->assertEquals('bar', $foo->getParam('foo'));
        $this->assertEquals('bob', $foo->getParam('boo'));
    }

    public function testReturnsDifferentInstance()
    {
        $bar = new Asset;
        $bar->set('Proem\Service\Asset\Bar', function() {
            return new Bar;
        });

        $one = $bar->get();
        $this->assertInstanceOf('Proem\Service\Asset\Bar', $one);

        $two = $bar->get();
        $this->assertInstanceOf('Proem\Service\Asset\Bar', $two);

        $this->assertNotSame($one, $two);

    }

    public function testSingleReturnsSameInstance()
    {
        $bar = new Asset;
        $bar->set('Proem\Service\Asset\Bar', $bar->single(function() {
            return new Bar;
        }));

        $one = $bar->get();
        $this->assertInstanceOf('Proem\Service\Asset\Bar', $one);

        $two = $bar->get();
        $this->assertInstanceOf('Proem\Service\Asset\Bar', $two);

        $this->assertSame($one, $two);

    }

    public function testAssetManagerCanStoreAndRetrieve()
    {
        $bar = new Asset;
        $bar->set('Proem\Service\Asset\Bar', function() {
            return new Bar;
        });

        $am = new Manager;
        $am->set('bar', $bar);

        $this->assertInstanceOf('Proem\Service\Asset\Bar', $am->get('bar'));
    }

    public function testAssetProvides()
    {
        $bar = new Asset;
        $bar->set('Proem\Service\Asset\Bar', function() {
            return new Bar;
        });
        $this->assertEquals('Proem\Service\Asset\Bar', $bar->provides());
    }

    public function testManagerProvides()
    {
        $bar = new Asset;
        $bar->set('Proem\Service\Asset\Bar', function() {
            return new Bar;
        });

        $am = new Manager;
        $am->set('bar', $bar);
        $this->assertTrue($am->provides('Proem\Service\Asset\Bar'));
        $this->assertTrue($am->provides('bar', 'Proem\Service\Asset\Bar'));
    }

    public function testRetrieveByProvides()
    {
        $bar = new Asset;
        $bar->set('Proem\Service\Asset\Bar', function() {
            return new Bar;
        });

        $foo = new Asset;
        $foo->set('Proem\Service\Asset\Foo', function() {
            return new Foo;
        });

        $am = new Manager;
        $am->set('bar', $bar)->set('foo', $foo);

        $this->assertInstanceOf('Proem\Service\Asset\Bar', $am->getProvided('Proem\Service\Asset\Bar'));
    }

    public function testCanGetDepsThroughManager()
    {
        $bar = new Asset;
        $bar->set('Proem\Service\Asset\Bar', function() {
            return new Bar;
        });

        $foo = new Asset;
        $foo->set('Proem\Service\Asset\Foo', function($a, $am) {
            $f = new Foo('something');
            $f->setBar($am->get('bar'));
            return $f;
        });

        $am = new Manager;
        $am->set('foo', $foo)->set('bar', $bar);

        $this->assertInstanceOf('Proem\Service\Asset\Bar', $am->get('bar'));
        $this->assertInstanceOf('Proem\Service\Asset\Foo', $am->get('foo'));
        $this->assertInstanceOf('Proem\Service\Asset\Bar', $am->get('foo')->getBar());
    }

    public function testManagerHas()
    {
        $bar = new Asset;
        $bar->set('Proem\Service\Asset\Bar', function() {
            return new Bar;
        });

        $am = new Manager;
        $am->set('bar', $bar);

        $this->assertTrue($am->has('bar'));
    }
}
