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
use \Thapp\Image\Traits\Scaling;
use \Thapp\Image\Driver\ImDriver;
use \Thapp\Image\Driver\BinLocatorInterface;

/**
 * Class: JitImageImDriverTest
 *
 * @uses JitImageDriverTest
 *
 * @package
 * @version
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ImDriverTest extends DriverTest
{

    use Scaling;
    /**
     * setUp
     *
     * @access protected
     * @return mixed
     */
    protected function setUp()
    {
        parent::setUp();

        $locator = m::mock('Thapp\Image\Driver\BinLocatorInterface');
        $locator->shouldReceive('getConverterPath')->andReturn($this->locateConvertBinary());
        $this->driver = new ImDriver($this->loaderMock, $locator);
    }

    /**
     * @test
     */
    public function testLoad()
    {
        $image = $this->createTestImage();

        $this->driver->load($image);

        $source = $this->getPropertyValue('source', $this->driver);
        $this->assertEquals($this->sourceFile, $source);
    }


    /**
     * @test
     * @dataProvider filterDataProvider
     */
    public function testOwnFilter($filter, $expectation = null)
    {
        return null;
    }

    /**
     * locateConvertBinary
     *
     * @access private
     * @return mixed
     */
    private function locateConvertBinary()
    {
        $paths = '/usr/local/bin,/usr/bin,/bin,./bin,../bin';
        $bin = null;

        foreach (explode(',', $paths) as $path) {

            if (file_exists($bin = $path . '/convert')) {
                break;
            }
        }

        return $bin;
    }

    /**
     * @test
     * @dataProvider percentualResizeProvider
     */
    public function testFilterPercentualResize($w, $h, $percent, $expected)
    {
        parent::testFilterPercentualResize($w, $h, $percent, $expected);
        $dimensions = $this->percentualScale($w, $h, $percent, $this->ratio($w, $h));

        $this->assertSame($expected, array_values($dimensions));
    }

    /**
     * @test
     * @dataProvider pixelLimitProvider
     */
    public function testFilterPixelLimit($w, $h, $limit)
    {
        parent::testFilterPixelLimit($w, $h, $limit);
        $pxl = $this->pixelLimit($w, $h, $limit, $this->ratio($w, $h));
        list($tw, $th) = getimagesize($this->writeTestImage($this->driver));

        $this->assertSame(array_values($pxl), [$tw, $th]);
    }
}
