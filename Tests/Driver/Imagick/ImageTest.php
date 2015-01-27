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
use Thapp\Image\Metrics\Box;
use Thapp\Image\Metrics\Point;
use Thapp\Image\Metrics\Gravity;
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
    public function itShouldPreserveAlpha()
    {
        //$image = $this->loadImage($this->asset('transparent4.png'));
        //$this->assertAlphaChannel($image, Imagick::ALPHACHANNEL_ACTIVATE);

        //$image = $this->loadImage($this->asset('transparent4.png'));
        //$image->edit()->rotate(45, $c = $image->getPalette()->getColor([255,255,255,0]));
        //$this->assertAlphaChannel($image, Imagick::ALPHACHANNEL_ACTIVATE);

        $image = $this->loadImage($this->asset('transparent4.png'));
        //$image->edit()->canvas(new Box(400, 400), new Point(100, 100), $image->getPalette()->getColor([0,0,0,0.2]));
        //$image->edit()->extent(new Box(400, 400), new Point(100, 100));
        //$this->assertAlphaChannel($image, Imagick::ALPHACHANNEL_ACTIVATE);
        $image->setGravity(new Gravity(1));
        $image->edit()->crop(new Box(100, 100));

        $image->save($this->asset('trans_crop_im.png'));
    }
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
            $this->markTestIncomplete();
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
