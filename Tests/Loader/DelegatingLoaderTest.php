<?php

/**
 * This File is part of the Thapp\Image\Tests\Driver\Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Loader;

use \Mockery as m;
use \Thapp\Image\Loader\FilesystemLoader;
use \Thapp\Image\Loader\DelegatingLoader;

/**
 * @class DelegatingLoaderTest
 * @package Thapp\Image\Tests\Driver\Loader
 * @version $Id$
 */
class DelegatingLoaderTest extends \PHPUnit_Framework_TestCase
{

    protected function setUp()
    {
    }

    protected function tearDown()
    {
        m::close();
    }

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Thapp\Image\Loader\LoaderInterface', new DelegatingLoader);
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

    /** @test */
    public function itShouldCloneItsLoaders()
    {
        $loader = new DelegatingLoader([
            $fsl = new FilesystemLoader
        ]);

        $this->assertTrue($loader->supports($file = __DIR__.'/Fixures/image.gif'));
        $this->assertSame($file, $loader->load($file));
        $this->assertSame($file, $loader->getSource());

        $clone = clone($loader);

        $this->assertNull($clone->getSource());
    }

    protected function getLoaderMock()
    {
        $mock = m::mock('\Thapp\Image\Loader\LoaderInterface');
        $mock->shouldreceive('clean')->andReturn(null);
        return $mock;
    }
}
