<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Driver;

use Thapp\Image\Metrics\Box;
use Thapp\Image\Metrics\Point;
use Thapp\Image\Metrics\Gravity;
use Thapp\Image\Color\Rgb;

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

        $this->assertSame('jpeg', $image->getFormat());

        $image = $this->newImage(200, 100, 'png');

        $this->assertSame('png', $image->getFormat());
        $image->setFormat('JPG');
        $this->assertSame('jpeg', $image->getFormat());
    }

    /** @test */
    public function itShouldReturnEditInstance()
    {
        $image = $this->newImage(200, 100);
        $this->assertInstanceof('Thapp\Image\Driver\EditInterface', $image->edit());
    }


    ///** @test */
    //public function itShouldBeInstantiable()
    //{
    //    $this->assertInstanceof('Thapp\Image\Driver\ImageInterface', $this->newImage(100, 100));
    //}

    ///** @test */
    //public function itShouldRotateImage()
    //{
    //    $image = $this->newImage(200, 100);
    //    $image->rotate(90);

    //    $this->assertSame(100, $image->getWidth());
    //    $this->assertSame(200, $image->getHeight());
    //}

    /** @test */
    public function itShouldCropWithGravity()
    {
        //$image->save($this->asset('pattern_copy_'.$this->getDriverName().'.png'));

        //if (false === $image = $this->loadImage($file = $this->asset('google.png'))) {
        //    $this->fail(sprintf('Loading of "%s" image file %s failed.', $this->getDriverName(), $file));
        //}

        //var_dump($image->getPalette());

        //var_dump($color = $image->getColorAt(new Point(240, 150)));
        //var_dump($color->getColor());
        //var_dump($color->getColorAsString());
        //$image->destroy();
        //$image->setGravity(new Gravity(5));
        //foreach ($image->coalesce() as $frame) {
            //$frame->edit()->crop(new Box(100, 100));
        //}

        //$image->save($this->asset('animated_'.$this->getDriverName().'.gif'));
    }

    /** @test */
    public function itShouldCopyInstance()
    {
        $image = $this->newImage(200, 200);
        $copy  = $image->copy();

        $this->assertFalse($image === $copy, 'Image should not equal copy.');
        $this->assertFalse($image->frames() === $copy->frames(), 'frames should not equal copied frames.');
        $this->assertFalse($image->getMetaData() === $copy->getMetaData(), 'metadata should not equal copied metadata.');
        $this->assertFalse($image->getPalette() === $copy->getPalette(), 'palette should not equal copied palette.');
    }

    /** @test */
    public function itShouldGetColorAtPixel()
    {
        $image = $this->loadImage($file = $this->asset('pattern.png'));

        $colorA = $image->getColorAt(new Point(0, 0));
        $colorB = $image->getColorAt(new Point(200, 0));

        $this->assertInstanceof('Thapp\Image\Color\RgbInterface', $colorA);
    }

    abstract protected function newImage($w, $h, $format = 'jpeg');
    abstract protected function getDriverName();
    abstract protected function loadImage($file);

    protected function getImagePath($resource)
    {
        $meta = stream_get_meta_data($resource);

        return $meta['uri'];
    }

    protected function setUp()
    {
        $this->assets = __DIR__.'/Fixures';
    }
}
