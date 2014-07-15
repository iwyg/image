<?php

/**
 * This File is part of the \Users\malcolm\www\image\src\Thapp\Image\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests;

use \Mockery as m;
use \Thapp\Image\Processor;

/**
 * @class ProcessorTest
 * @package \Users\malcolm\www\image\src\Thapp\Image\Tests
 * @version $Id$
 */
class ProcessorTest extends \PHPUnit_Framework_TestCase
{
    private $driver;

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof(
            'Thapp\Image\ProcessorInterface',
            new Processor(m::mock('Thapp\Image\Driver\DriverInterface'))
        );
    }

    /** @test */
    public function itShouldLoadASource()
    {
        $loaded = false;
        $source = 'sourcefile.jpg';

        $this->driver->shouldReceive('load')->with($source)->andReturnUsing(function () use (&$loaded) {
            $loaded = true;
        });

        $proc = new Processor($this->driver);
        $proc->load($source);

        $this->assertTrue($loaded);
    }

    /** @test */
    public function itShouldProcessAnImage()
    {
        $params = [
            'mode'          => 0,
            'width'         => null,
            'height'        => null,
            'gravity'       => null,
            'background'    => null,
            'filter'        => []
        ];

        $proc = new Processor($this->newDriver());

        $this->driver->shouldReceive('setTargetSize')->with(null, null);
        $this->driver->shouldReceive('process');

        $proc->process($params);

        $params = [
            'mode'          => 1,
            'width'         => 100,
            'height'        => 100,
            'gravity'       => null,
            'background'    => null,
            'filter'        => []
        ];

        $proc = new Processor($this->newDriver());

        $this->driver->shouldReceive('setTargetSize')->with(100, 100);
        $this->driver->shouldReceive('filter')->with('resize', []);
        $this->driver->shouldReceive('process');

        $proc->process($params);

        $params = [
            'mode'          => 2,
            'width'         => 100,
            'height'        => 200,
            'gravity'       => 5,
            'background'    => null,
            'filter'        => []
        ];

        $proc = new Processor($this->newDriver());
        $this->driver->shouldReceive('setTargetSize')->with(100, 200);
        $this->driver->shouldReceive('filter')->with('cropScale', [5]);
        $this->driver->shouldReceive('process');

        $proc->process($params);

        $params = [
            'mode'          => 3,
            'width'         => 100,
            'height'        => 200,
            'gravity'       => 5,
            'background'    => 'fff',
            'filter'        => []
        ];

        $proc = new Processor($this->newDriver());
        $this->driver->shouldReceive('setTargetSize')->with(100, 200);
        $this->driver->shouldReceive('filter')->with('crop', [5, 'fff']);
        $this->driver->shouldReceive('process');

        $proc->process($params);

        $params = [
            'mode'          => 4,
            'width'         => 100,
            'height'        => 200,
            'gravity'       => null,
            'background'    => null,
            'filter'        => []
        ];

        $proc = new Processor($this->newDriver());
        $this->driver->shouldReceive('setTargetSize')->with(100, 200);
        $this->driver->shouldReceive('filter')->with('resizeToFit', []);
        $this->driver->shouldReceive('process');

        $proc->process($params);

        $params = [
            'mode'          => 5,
            'width'         => 50,
            'height'        => null,
            'gravity'       => null,
            'background'    => null,
            'filter'        => []
        ];

        $proc = new Processor($this->newDriver());
        $this->driver->shouldReceive('setTargetSize')->with(50, null);
        $this->driver->shouldReceive('filter')->with('percentualScale', [50]);
        $this->driver->shouldReceive('process');

        $proc->process($params);

        $params = [
            'mode'          => 6,
            'width'         => 5000000,
            'height'        => null,
            'gravity'       => null,
            'background'    => null,
            'filter'        => []
        ];

        $proc = new Processor($this->newDriver());
        $this->driver->shouldReceive('setTargetSize')->with(5000000, null);
        $this->driver->shouldReceive('filter')->with('resizePixelCount', [5000000]);
        $this->driver->shouldReceive('process');

        $proc->process($params);


        $params = [
            'mode'          => 0,
            'width'         => null,
            'height'        => null,
            'gravity'       => null,
            'background'    => null,
            'filter'        => [
                'circle' => $f = ['s' => 12]
            ]
        ];

        $proc = new Processor($this->newDriver());

        $this->driver->shouldReceive('setTargetSize')->with(null, null);
        $this->driver->shouldReceive('filter')->with('circle', $f);
        $this->driver->shouldReceive('process');

        $proc->process($params);

    }

    /** @test */
    public function itShouldWriteToFile()
    {
        $writer   = m::mock('Thapp\Image\Writer\WriterInterface');
        $writer->shouldReceive('write')->with('target.jpg', 'content');


        $proc = new Processor($this->driver);

        try {
            $proc->writeToFile('target.jpg', null, $writer);
        } catch (\BadMethodCallException $e) {
            $this->assertSame('No source loaded.', $e->getMessage());
        }

        $proc = new Processor($this->driver);

        $this->driver->shouldReceive('load')->andReturn('file');
        $this->driver->shouldReceive('getOutputType')->andReturn('jpg');
        $this->driver->shouldReceive('getImageBlob')->andReturn('content');

        $proc->load('file');

        $proc->writeToFile('target.jpg', null, $writer);
    }

    /** @test */
    public function itShouldWriteResourceToFile()
    {
        $resource = m::mock('Thapp\Image\Resource\ResourceInterface');
        $writer   = m::mock('Thapp\Image\Writer\WriterInterface');

        $resource->shouldReceive('getContents')->andReturn('content');
        $writer->shouldReceive('write')->with('target.jpg', 'content');

        $this->driver->shouldReceive('getOutputType')->andReturn('jpg');

        $proc = new Processor($this->driver);

        $proc->writeResourceToFile($resource, 'target.jpg', $writer);

        $proc->writeToFile('target.jpg', $resource, $writer);
    }

    /** @test */
    public function itShouldSetQualityOnDriver()
    {
        $q = 0;

        $this->driver->shouldReceive('setQuality')->with(80)->andReturnUsing(function ($quality) use (&$q) {
            $q = $quality;
        });

        $proc = new Processor($this->driver);
        $proc->setQuality(80);

        $this->assertSame(80, $q);
    }

    /** @test */
    public function itShouldSetPutPutTypeOnDriver()
    {
        $f = 0;

        $this->driver->shouldReceive('setOutputType')->with('jpg')->andReturnUsing(function ($format) use (&$f) {
            $f = $format;
        });

        $proc = new Processor($this->driver);
        $proc->setFileFormat('jpg');

        $this->assertSame('jpg', $f);
    }

    /** @test */
    public function itShouldGetContentFromDriver()
    {
        $this->driver->shouldReceive('getImageBlob')->andReturn('content');

        $proc = new Processor($this->driver);
        $this->assertSame('content', $proc->getContents());
    }

    /** @test */
    public function itShouldCallGetOutPutTypeOnDriver()
    {
        $this->driver->shouldReceive('getOutputType')->andReturn('jpg');

        $proc = new Processor($this->driver);
        $this->assertSame('jpg', $proc->getFileFormat());
    }

    /** @test */
    public function itShouldCallGetSourceTypeOnDriver()
    {
        $this->driver->shouldReceive('getSourceType')->with(true)->andReturn('jpg');
        $this->driver->shouldReceive('getSourceType')->with(false)->andReturn('image/jpeg');

        $proc = new Processor($this->driver);

        $this->assertSame('image/jpeg', $proc->getSourceMimeType());
        $this->assertSame('jpg', $proc->getSourceFormat());
    }

    /** @test */
    public function itShouldCallgetOutputMimeTypeOnDriver()
    {
        $this->driver->shouldReceive('getOutputMimeType')->andReturn('image/jpeg');
        $proc = new Processor($this->driver);
        $this->assertSame('image/jpeg', $proc->getMimeType());
    }

    /** @test */
    public function itShouldGetLasModTime()
    {
        $time = filemtime(__FILE__);

        $this->driver->shouldReceive('isProcessed')->andReturn(false);
        $this->driver->shouldReceive('getSource')->andReturn(__FILE__);
        $proc = new Processor($this->driver);
        $this->assertSame($time, $proc->getLastModTime());
    }

    /** @test */
    public function itShouldGetLasModTimeWhenProcessed()
    {
        $time = time();

        $this->driver->shouldReceive('isProcessed')->andReturn(true);
        $proc = new Processor($this->driver);
        $this->assertSame($time, $proc->getLastModTime());
    }

    /** @test */
    public function itShoudGetTheTargetSize()
    {

    }

    /** @test */
    public function itShouldGetSourceFromDriver()
    {
        $loaded = false;
        $source = 'sourcefile.jpg';

        $this->driver->shouldReceive('load')->with($source)->andReturnUsing(function () use (&$loaded) {
            $loaded = true;
        });

        $this->driver->shouldReceive('getSource')->andReturnUsing(function () use (&$loaded, $source) {
            if ($loaded) {
                return $source;
            }
        });
        $proc = new Processor($this->driver);

        $proc->load($source);

        $this->assertSame($source, $proc->getSource());
    }

    /** @test */
    public function itShouldGetItsDriver()
    {
        $proc = new Processor($driver = m::mock('Thapp\Image\Driver\DriverInterface'));

        $this->assertSame($driver, $proc->getDriver());
    }

    protected function newDriver()
    {
        return $this->driver = m::mock('Thapp\Image\Driver\DriverInterface');
    }

    protected function setUp()
    {
        $this->newDriver();
    }

    protected function tearDown()
    {
        m::close();
    }
}
