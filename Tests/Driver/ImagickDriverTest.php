<?php

/**
 * This File is part of the tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Driver;

use \Mockery as m;
use \Thapp\Image\Driver\ImagickDriver;

/**
 * @class ImagickDriverTest extends DriverTest
 * @see DriverTest
 *
 * @package Thapp\Image\Tests\Driver
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ImagickDriverTest extends DriverTest
{

    /**
     * setUp
     *
     * @access protected
     * @return mixed
     */
    protected function setUp()
    {

        if (!class_exists('\Imagick')) {
            $this->markTestSkipped();
        }

        parent::setUp();
        $this->driver = new ImagickDriver($this->loaderMock);
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
        $this->assertInstanceOf('\Imagick', $resource);
    }

    /**
     * @test
     * @dataProvider filterDataProvider
     */
    public function testOwnFilter($filter, $expectation = null)
    {
        return null;
    }
}
