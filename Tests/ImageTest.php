<?php

/**
 * This File is part of the Thapp\Image\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests;

use \Mockery as m;
use \Thapp\Image\Image;
use \Thapp\Image\ProcessorInterface;
use \Thapp\Image\Cache\CacheInterface;

/**
 * @class ImageTest
 * @package Thapp\Image\Tests
 * @version $Id$
 */
class ImageTest extends \PHPUnit_Framework_TestCase
{
    private $proc;

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Thapp\Image\Image', new Image($this->proc));
    }

    /** @test */
    public function itShouldCreateInstanceFromFactory()
    {
        $img = Image::create();
        $this->assertInstanceof('Thapp\Image\Driver\ImagickDriver', $img->getProcessor()->getDriver());

        $img = Image::create(null, Image::DRIVER_IMAGICK);
        $this->assertInstanceof('Thapp\Image\Driver\ImagickDriver', $img->getProcessor()->getDriver());

        $img = Image::create(null, Image::DRIVER_GD);
        $this->assertInstanceof('Thapp\Image\Driver\GdDriver', $img->getProcessor()->getDriver());

        $img = Image::create(null, Image::DRIVER_IM);
        $this->assertInstanceof('Thapp\Image\Driver\ImDriver', $img->getProcessor()->getDriver());
    }

    /** @test */
    public function itShouldLoadImagesFromCache()
    {
        $resource = m::mock('Resource');

        $this->proc->shouldReceive('isProcessed')->andReturn(false);
        $this->proc->shouldReceive('close');
        $this->proc->shouldReceive('getContents')->andReturn('image_data');
        $this->proc->shouldReceive('writeToFile')->with('target.jpg', $resource);

        $cache = m::mock('Thapp\Image\Cache\CacheInterface');
        $cache->shouldReceive('createKey')
            ->with('source.jpg', '0/filter:circle;s=12', '', 'jpg')
            ->andReturn('cache_key');

        $cache->shouldReceive('has')
            ->with('cache_key')->andReturn(true);

        $cache->shouldReceive('get')
            ->with('cache_key', CacheInterface::CONTENT_RESOURCE)->andReturn($resource);

        $img = new Image($this->proc);
        $img->setImageCache($cache);

        $this->assertSame($cache, $img->getImageCache());

        $img->load('source.jpg')->filter('circle;s=12')->save('target.jpg');
    }

    /** @test */
    public function itShouldContinueProcessingIfNotCached()
    {
        $resource = m::mock('Resource');

        $proc = false;

        $this->proc->shouldReceive('isProcessed')->andReturn(false);
        $this->proc->shouldReceive('close');
        $this->proc->shouldReceive('load')->with('source.jpg');
        $this->proc->shouldReceive('process')->andReturnUsing(function () use (&$proc) {
            $proc = true;
        });

        $this->proc->shouldReceive('getContents')->andReturn('image_data');

        $this->proc->shouldReceive('writeToFile')->with('target.jpg', null);

        $cache = m::mock('Thapp\Image\Cache\CacheInterface');
        $cache->shouldReceive('createKey')
            ->with('source.jpg', '0/filter:circle;s=12', '', 'jpg')
            ->andReturn('cache_key');

        $cache->shouldReceive('has')
            ->with('cache_key')->andReturn(false);

        $cache->shouldReceive('set')
            ->with('cache_key', $this->proc);

        $img = new Image($this->proc);
        $img->setImageCache($cache);

        $img->load('source.jpg')->filter('circle;s=12')->save('target.jpg');

        $this->assertTrue($proc);
    }

    /** @test */
    public function itShouldGetImageContents()
    {
        $this->proc->shouldReceive('isProcessed')->andReturn(true);
        $this->proc->shouldReceive('close');
        $this->proc->shouldReceive('getContents')->andReturn('image_data');

        $img = new Image($this->proc);

        $this->assertSame('image_data', $img->getImageData());
    }

    /** @test */
    public function itShouldSetQualityOnProcessor()
    {
        $q = null;

        $this->proc->shouldReceive('setQuality')->with(60)->andReturnUsing(function ($qlt) use (&$q) {
            $q = $qlt;
        });

        $img = new Image($this->proc);
        $img->quality(60);

        $this->assertSame($q, 60);
    }

    /** @test */
    public function itShouldLoadSources()
    {
        $img = new Image($this->proc);
        $img->load('somesource');
    }

    /** @test */
    public function itShouldPassArgsOnResize()
    {
        $args = $this->getArgs(ProcessorInterface::IM_RESIZE, 100, 100);

        $this->prepareProcess('source', 'target', $args);

        $img = new Image($this->proc);
        $img
            ->load('source')
            ->resize(100, 100)
            ->save('target');
    }

    /** @test */
    public function itShouldPassArgsOnCrop()
    {
        $args = $this->getArgs(ProcessorInterface::IM_CROP, 100, 100, 5, 'fff');

        $this->prepareProcess('source', 'target', $args);

        $img = new Image($this->proc);
        $img
            ->load('source')
            ->crop(100, 100, 5, 'fff')
            ->save('target');
    }

    /** @test */
    public function itShouldNotSetInvalidBackground()
    {

        $args = $this->getArgs(ProcessorInterface::IM_CROP, 100, 100, 5);

        $this->prepareProcess('source', 'target', $args);

        $img = new Image($this->proc);
        $img
            ->load('source')
            ->crop(100, 100, 5, 'abcd')
            ->save('target');
    }

    /** @test */
    public function itShouldPassArgsOnCropAndResize()
    {
        $args = $this->getArgs(ProcessorInterface::IM_SCALECROP, 100, 100, 5);

        $this->prepareProcess('source', 'target', $args);

        $img = new Image($this->proc);
        $img
            ->load('source')
            ->cropAndResize(100, 100, 5)
            ->save('target');
    }

    /** @test */
    public function itShouldPassArgsOnFit()
    {
        $args = $this->getArgs(ProcessorInterface::IM_RSIZEFIT, 100, 100);

        $this->prepareProcess('source', 'target', $args);

        $img = new Image($this->proc);
        $img
            ->load('source')
            ->fit(100, 100)
            ->save('target');
    }

    /** @test */
    public function itShouldPassArgsOnGet()
    {
        $args = $this->getArgs(ProcessorInterface::IM_NOSCALE);

        $this->prepareProcess('source', 'target', $args);

        $img = new Image($this->proc);
        $img
            ->load('source')
            ->get()
            ->save('target');
    }

    /** @test */
    public function itShouldPassArgsOnPixel()
    {
        $args = $this->getArgs(ProcessorInterface::IM_RSIZEPXCOUNT, 2000000);

        $this->prepareProcess('source', 'target', $args);

        $img = new Image($this->proc);
        $img
            ->load('source')
            ->pixel(2000000)
            ->save('target');
    }

    /** @test */
    public function itShouldPassArgsOnScale()
    {
        $args = $this->getArgs(ProcessorInterface::IM_RSIZEPERCENT, 50);

        $this->prepareProcess('source', 'target', $args);

        $img = new Image($this->proc);
        $img
            ->load('source')
            ->scale(50)
            ->save('target');
    }

    /** @test */
    public function itShouldPassFilters()
    {
        $args = $this->getArgs(
            ProcessorInterface::IM_NOSCALE,
            null,
            null,
            null,
            null,
            [
                'circle' => ['s' => 12]
            ]
        );

        $this->prepareProcess('source', 'target', $args);

        $img = new Image($this->proc);
        $img
            ->load('source')
            ->filter('circle;s=12')
            ->save('target');
    }

    /** @test */
    public function itShouldPassFiltersWhenAdded()
    {
        $args = $this->getArgs(
            ProcessorInterface::IM_NOSCALE,
            null,
            null,
            null,
            null,
            [
                'circle' => ['s' => 12]
            ]
        );

        $this->prepareProcess('source', 'target', $args);

        $img = new Image($this->proc);
        $img
            ->load('source')
            ->addFilter('circle', ['s' =>12])
            ->save('target');
    }

    /** @test */
    public function itShouldPassFiltersOnToJpeg()
    {
        $args = $this->getArgs(
            ProcessorInterface::IM_NOSCALE,
            null,
            null,
            null,
            null,
            [
                'convert' => ['f' => 'jpg']
            ]
        );

        $this->prepareProcess('source', 'target', $args);

        $img = new Image($this->proc);
        $img
            ->load('source')
            ->toJpeg()
            ->save('target');
    }

    /** @test */
    public function itShouldPassFiltersOnToPng()
    {
        $args = $this->getArgs(
            ProcessorInterface::IM_NOSCALE,
            null,
            null,
            null,
            null,
            [
                'convert' => ['f' => 'png']
            ]
        );

        $this->prepareProcess('source', 'target', $args);

        $img = new Image($this->proc);
        $img
            ->load('source')
            ->toPng()
            ->save('target');
    }

    /** @test */
    public function itShouldPassFiltersOnToGif()
    {
        $args = $this->getArgs(
            ProcessorInterface::IM_NOSCALE,
            null,
            null,
            null,
            null,
            [
                'convert' => ['f' => 'gif']
            ]
        );

        $this->prepareProcess('source', 'target', $args);

        $img = new Image($this->proc);
        $img
            ->load('source')
            ->toGif()
            ->save('target');
    }


    protected function prepareProcess($source, $target, array $args = [])
    {
        $this->proc->shouldReceive('isProcessed')->andReturn(false);
        $this->proc->shouldReceive('load')->with($source);
        $this->proc->shouldReceive('writeToFile')->with($target, m::any());
        $this->proc->shouldReceive('close');

        $this->proc->shouldReceive('process')->andReturnUsing(function ($arguments) use (&$args) {
            $this->assertSame($args, $arguments);
        });
    }

    protected function getArgs($mode = null, $width = null, $height = null, $gravity = null, $background = null, $filter = [])
    {
        return compact('mode', 'width', 'height', 'gravity', 'background', 'filter');
    }

    protected function setUp()
    {
        $this->proc = m::mock('Thapp\Image\ProcessorInterface');
    }

    protected function tearDown()
    {
        m::close();
    }
}
