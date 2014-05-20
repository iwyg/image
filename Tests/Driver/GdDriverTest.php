<?php

/**
 * This File is part of the Thapp\Image package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Driver;

use \Mockery as m;
use \Thapp\Image\Driver\GdDriver;
use \Thapp\Image\Tests\Driver\Stubs\Filter\GdFilterStub as GdFilter;

/**
 * @class GdDriverTest extends DriverTest
 * @see DriverTest
 *
 * @package Thapp\Image\Tests\Driver
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class GdDriverTest extends DriverTest
{

    /**
     * setUp
     *
     * @access protected
     * @return mixed
     */
    protected function setUp()
    {
        parent::setUp();
        $this->driver = new GdDriver($this->loaderMock);
    }

    /**
     * @test
     */
    public function testLoad()
    {
        $image = $this->createTestImage();

        $this->driver->load($image);

        $source = $this->getPropertyValue('source', $this->driver);
        $resource = $this->getPropertyValue('resource', $this->driver);

        $this->assertEquals($this->sourceFile, $source);
        $this->assertTrue(is_resource($resource));

        $this->assertTrue(is_resource($this->driver->getResource()));
        $this->assertEquals('gd', get_resource_type($this->driver->getResource()));
    }

    /** @test */
    public function itShouldReturnTheImageContents()
    {
        $image = $this->createTestImage(10, 10);
        $this->loaderMock->shouldReceive('getSource')->andReturn($image);
        $this->driver->load($image);

        $this->assertStringEqualsFile($image, $this->driver->getImageBlob());
    }

    /**
     * @test
     * @dataProvider filterDataProvider
     */
    public function testOwnFilter($filter, $expectation = null)
    {
        $image = $this->createTestImage();

        $this->driver->load($image);

        $mock = m::mock('reporter');
        $mock->shouldReceive('run')->once()->andReturnUsing(function ($message = null) {
            $this->assertEquals('success', $message);
        });


        GdFilter::setMockReporter($mock);

        $this->driver->registerFilter('test', '\Thapp\Image\Tests\Driver\Stubs\Filter\GdFilterStub');

        $this->driver->filter('test', []);
    }
}
