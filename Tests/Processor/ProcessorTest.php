<?php

/**
 * This File is part of the Thapp\Image\Tests\Processor package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Processor;

use \Mockery as m;
use \Thapp\Image\Processor;
use \Thapp\Image\ProcessorInterface;
use \Thapp\Image\Driver\DriverInterface;
use \Thapp\Image\Writer\WriterInterface;

/**
 * @package Thapp\Image\Tests\Processor
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ProcessorTest extends \PHPUnit_Framework_TestCase
{

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('\Thapp\Image\Processor', new Processor($this->createDriver(), $this->createWriter()));
    }

    /**
     * @test
     * @dataProvider qualityProvider
     */
    public function qualityShouldBeSettable($quality)
    {
        $q = false;
        $driver = $this->createDriver();
        $writer = $this->createWriter();

        $driver->shouldReceive('setQuality')->with($quality)->andReturnUsing(function () use (&$q) {
            $q = true;
        });

        $processor = new Processor($driver, $writer);

        try {
            $processor->setQuality($quality);
            $this->assertTrue($q, '->setQuality() should call setQuality() on driver');
        } catch (\Exception $e) {
            $this->fail(sprintf('->setQuality() should not throw an exception: [%s]', $e->getMessage()));
        }

        $this->assertTrue($q);
    }

    /** @test */
    public function itShouldLoadAFileResource()
    {
        $loaded = false;
        $driver = $this->createDriver();
        $writer = $this->createWriter();

        $source = 'somefile';

        $driver->shouldReceive('load')->with($source)->once()->andReturnUsing(function () use (&$loaded) {
            $loaded = true;
        });

        $processor = new Processor($driver, $writer);

        try {
            $processor->load($source);
            $this->assertTrue($loaded, '->load() should call load() on driver');
        } catch (\Exception $e) {
            $this->fail(sprintf('->load() should not throw an exception: [%s]', $e->getMessage()));
        }

        try {
            $loaded = false;
            $processor = new Processor($driver, $writer);
            $this->assertFalse($loaded, '->load() should not call load() on driver');
        } catch (\Exception $e) {
            $this->fail(sprintf('->load() should not throw an exception: [%s]', $e->getMessage()));
        }
    }

    /**
     * @test
     */
    public function testClose()
    {
        $closed = false;
        $driver = $this->createDriver();
        $writer = $this->createWriter();

        $driver->shouldReceive('load')->andReturn(true);

        $driver->shouldReceive('clean')->andReturnUsing(function () use (&$closed) {
            $closed = true;
        });

        $processor = new Processor($driver, $writer);

        try {
            $processor->close();
            $this->assertFalse($closed, '->close() should not call clean() on driver if not loaded');
        } catch (\Exception $e) {
            $this->fail(sprintf('->close() should not throw an exception: [%s]', $e->getMessage()));
        }

        $processor->load('somefile');
        try {
            $processor->close();
            $this->assertTrue($closed, '->close() should call clean() on driver if loaded');
        } catch (\Exception $e) {
            $this->fail(sprintf('->close() should not throw an exception: [%s]', $e->getMessage()));
        }
    }

    /**
     * @test
     * @dataProvider processParameterProvider
     */
    public function testProcess(array $parameters)
    {
        $size = false;
        $filter = false;
        $processed = false;

        $driver = $this->createDriver();
        $writer = $this->createWriter();

        $driver->shouldReceive('setTargetSize')->andReturnUsing(function () use (&$size) {
            $size = true;
        });

        $driver->shouldReceive('process')->andReturnUsing(function () use (&$processed) {
            $processed = true;
        });

        if (isset($parameters['mode'])) {
            $driver->shouldReceive('filter')->andReturnUsing(function () use (&$filter) {
                $filter = true;
            });
        }


        $processor = new Processor($driver, $writer);

        $processor->process($parameters);

        $this->assertTrue($size, '->process() should call setTargetSize() on driver');
        $this->assertTrue($processed, '->process() should call process() on driver');

        if (isset($parameters['mode'])) {
            $this->assertTrue($processed, '->process() should call filter() on driver');
        }
    }

    /** @test */
    public function itShouldSetFileformatOnDriver()
    {

        $driver = $this->createDriver();
        $writer = $this->createWriter();

        $format = '';

        $driver->shouldReceive('setOutputType')->with('jpg')->andReturnUsing(function ($f) use (&$format) {
            $format = $f;
        });

        $processor = new Processor($driver, $writer);

        $processor->setFileFormat('jpg');

        $this->assertSame('jpg', $format);
    }

    /** @test */
    public function itShouldGetFileformatFromDriver()
    {

        $driver = $this->createDriver();
        $writer = $this->createWriter();

        $driver->shouldReceive('getOutputType')->andReturnUsing(function () {
            return 'jpg';
        });

        $processor = new Processor($driver, $writer);

        $format = $processor->getFileFormat();

        $this->assertSame('jpg', $format);
    }

    /** @test */
    public function itShouldGetSourceMimetypeFromDriver()
    {

        $driver = $this->createDriver();
        $writer = $this->createWriter();

        $driver->shouldReceive('getSourceType')->with(false)->andReturnUsing(function () {
            return 'image/png';
        });

        $processor = new Processor($driver, $writer);

        $format = $processor->getSourceMimeType();

        $this->assertSame('image/png', $format);
    }

    /** @test */
    public function itShouldGetMimetypeFromDriver()
    {

        $driver = $this->createDriver();
        $writer = $this->createWriter();

        $driver->shouldReceive('getOutputMimeType')->andReturnUsing(function () {
            return 'image/jpeg';
        });

        $processor = new Processor($driver, $writer);

        $format = $processor->getMimeType();

        $this->assertSame('image/jpeg', $format);
    }

    /** @test */
    public function itShouldGetSourceFormatFromDriver()
    {

        $driver = $this->createDriver();
        $writer = $this->createWriter();

        $driver->shouldReceive('getSourceType')->with(true)->andReturnUsing(function () {
            return 'png';
        });

        $processor = new Processor($driver, $writer);

        $format = $processor->getSourceFormat();

        $this->assertSame('png', $format);
    }

    /** @test */
    public function itShouldGrabContentFromDriver()
    {

        $driver = $this->createDriver();
        $writer = $this->createWriter();

        $driver->shouldReceive('getImageBlob')->andReturnUsing(function () {
            return 'content';
        });

        $processor = new Processor($driver, $writer);

        $contents = $processor->getContents();

        $this->assertSame('content', $contents);
    }

    public function processParameterProvider()
    {
        return [
            [[]],
            [['mode' => 1]],
            [['mode' => 2]],
            [['mode' => 3]],
            [['mode' => 4]],
            [['mode' => 5]],
            [['mode' => 6]],
        ];
    }

    public function qualityProvider()
    {
        return [
            [0],
            [10],
            [50],
            [80]
        ];
    }

    protected function createDriver(callable $setup = null)
    {
        $driver = m::mock('\Thapp\Image\Driver\DriverInterface');

        if (null !== $setup) {
            call_user_func($setup, $driver);
        }
        return $driver;
    }

    protected function createWriter(callable $setup = null)
    {
        $writer = m::mock('\Thapp\Image\Writer\WriterInterface');

        if (null !== $setup) {
            call_user_func($setup, $writer);
        }
        return $writer;
    }

    protected function tearDown()
    {
        m::close();
    }
}
