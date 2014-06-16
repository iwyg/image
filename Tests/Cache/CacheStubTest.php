<?php

/**
 * This File is part of the Thapp\Image\Tests\Cache package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Cache;


use \Mockery as m;
use Thapp\Image\Tests\Cache\Stubs\CacheStub;

/**
 * @class FileystemCacheTest
 * @package Thapp\Image\Tests\Cache
 * @version $Id$
 */
class CacheStubTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Thapp\Image\Cache\CacheInterface', new CacheStub);
    }

    /** @test */
    public function itShouldSetPrefix()
    {
        $c = new CacheStub;
        $c->setPrefix('foo');

        $this->assertSame('foo', $c->getPrefix());
    }

    /** @test */
    public function itShouldSetSuffix()
    {
        $c = new CacheStub;
        $c->setSuffix('foo');

        $this->assertSame('foo', $c->getSuffix());
    }

    /** @test */
    public function itShouldHaveAKey()
    {
        $c = new CacheStub;
        $this->assertFalse($c->has('bar'));
        $this->assertTrue($c->has('foo'));
        $this->assertSame('bar', $c->getSource('foo'));
    }
}
