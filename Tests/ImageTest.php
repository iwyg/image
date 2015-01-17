<?php

/*
 * This File is part of the Thapp\Image\Tests package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests;

use Thapp\Image\Metrics\Box;
use Thapp\Image\Metrics\Point;
use Thapp\Image\Metrics\Gravity;

/**
 * @class ImageTest
 *
 * @package Thapp\Image\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class ImageTest extends \PHPUnit_Framework_TestCase
{
    use ImageTestHelper;

    /** @test */
    public function itShouldGetImageFormat()
    {
        $image = $this->newImage(200, 100);

        var_dump($image->getFormat());
    }

    /** @test */
    public function itShouldCropImage()
    {
        $image = $this->newImage(200, 100);
        $image->crop(new Box(50, 50));

        $this->assertSame(50, $image->getHeight());
        $this->assertSame(50, $image->getWidth());
    }

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Thapp\Image\Driver\ImageInterface', $this->newImage(100, 100));
    }

    /** @test */
    public function itShouldRotateImage()
    {
        $image = $this->newImage(200, 100);
        $image->rotate(90);
    }

    abstract protected function newImage($w, $h, $format = 'jpeg');

    protected function getImagePath($resource)
    {
        $meta = stream_get_meta_data($resource);

        return $meta['uri'];
    }
}
