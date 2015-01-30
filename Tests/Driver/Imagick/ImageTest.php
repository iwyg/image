<?php

/*
 * This File is part of the Thapp\Image\Tests\Driver\Imagick package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Driver\Imagick;

use Imagick;
use Thapp\Image\Driver\Imagick\Image;
use Thapp\Image\Driver\Imagick\Source;
use Thapp\Image\Geometry\Size;
use Thapp\Image\Geometry\Point;
use Thapp\Image\Geometry\Gravity;
use Thapp\Image\Tests\Driver\ImageTest as AbstractImageTest;

/**
 * @class ImageTest
 *
 * @package Thapp\Image\Tests\Driver\Imagick
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ImageTest extends AbstractImageTest
{
    protected $handle;
    /** @test */
    public function itShouldGetImagick()
    {
        $image = $this->newImage(100, 100);
        $this->assertInstanceof('Imagick', $image->getImagick());
    }

    /** @test */
    public function imagickShouldBeSwapabled()
    {
        $image = $this->newImage(100, 100);
        $image->swapImagick($imagick = new \Imagick);

        $this->assertSame($imagick, $image->getImagick());
    }

    /** @test */
    public function itShouldDetectFrames()
    {
        $image = $this->newImage(100, 100);
        $this->assertFalse($image->hasFrames());
    }

    protected function loadImage($file)
    {
        $image = (new Source())->load($file);

        return $image;
    }

    protected function getDriverName()
    {
        return 'imagick';
    }

    protected function newImage($w, $h, $format = 'jpeg')
    {
        $resource = $this->getTestImage($w, $h, $format);
        $source = new Source();

        return $source->read($resource);
    }

    protected function setUp()
    {
        if (!class_exists('Imagick') || (isset($_ENV['IMAGE_DRIVER']) && 'imagick' !== $_ENV['IMAGE_DRIVER'])) {
            $this->markTestSkipped('Imagick extension not installed');
        }

        parent::setUp();
    }

    protected function tearDown()
    {
        if (is_resource($this->handle)) {
            fclose($this->handle);
        }
    }
}
