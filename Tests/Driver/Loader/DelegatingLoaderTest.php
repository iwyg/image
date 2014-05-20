<?php

/**
 * This File is part of the Thapp\Image\Tests\Driver\Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Driver\Loader;

use \Mockery as m;
use \Thapp\Image\Driver\Loader\DelegatingLoader;

/**
 * @class DelegatingLoaderTest
 * @package Thapp\Image\Tests\Driver\Loader
 * @version $Id$
 */
class DelegatingLoaderTest extends \PHPUnit_Framework_TestCase
{

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Thapp\Image\Driver\Loader\LoaderInterface', new DelegatingLoader);
    }

    /** @test */
    public function itShouldHitEachLoaderInstance()
    {
        $floader = $this->getLoaderMock();

        $floader->shouldReceive('supports')->andReturnUsing(function ($src) {
            return $src === 'image.jpg';
        });

        $floader->shouldReceive('load')->andReturnUsing(function ($src) {
            return $src;
        });

        $rloader = $this->getLoaderMock();

        $rloader->shouldReceive('supports')->andReturnUsing(function ($src) {
            return $src === 'http://example.com/image.jpg';
        });

        $rloader->shouldReceive('load')->andReturnUsing(function ($src) {
            return '/path/to/tmpfile';
        });

        $loader = new DelegatingLoader(
            [
                $floader,
                $rloader
            ]
        );

        $this->assertTrue($loader->supports('image.jpg'));
        $this->assertTrue($loader->supports('http://example.com/image.jpg'));
        $this->assertfalse($loader->supports('ftp://example.com/image.jpg'));

        $this->assertSame('image.jpg', $loader->load('image.jpg'));
        $this->assertSame('/path/to/tmpfile', $loader->load('http://example.com/image.jpg'));
    }

    /** @test */
    public function itShouldReturnTheSourceFile()
    {

        $floader = $this->getLoaderMock();

        $floader->shouldReceive('supports')->andReturnUsing(function ($src) {
            return $src === 'image.jpg';
        });

        $floader->shouldReceive('getSource')->andReturn('image.jpg');

        $floader->shouldReceive('load')->andReturnUsing(function ($src) {
            return $src;
        });

        $rloader = $this->getLoaderMock();

        $rloader->shouldReceive('supports')->andReturnUsing(function ($src) {
            return $src === 'http://example.com/image.jpg';
        });

        $rloader->shouldReceive('load')->andReturnUsing(function ($src) {
            return '/path/to/tmpfile';
        });

        $loader = new DelegatingLoader(
            [
                $floader,
                $rloader
            ]
        );

        $loader->supports('image.jpg');
        $this->assertSame('image.jpg', $loader->load('image.jpg'));
        $this->assertSame('image.jpg', $loader->getSource('image.jpg'));
    }

    protected function getLoaderMock()
    {
        $mock = m::mock('\Thapp\Image\Driver\Loader\LoaderInterface');
        $mock->shouldreceive('clean')->andReturn(null);
        return $mock;
    }
}
