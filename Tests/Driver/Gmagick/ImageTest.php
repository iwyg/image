<?php

/*
 * This File is part of the Thapp\Image\Tests\Driver\Imagick package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Driver\Gmagick;

use Thapp\Image\Driver\Gmagick\Image;
use Thapp\Image\Driver\Gmagick\Source;
use Thapp\Image\Metrics\Box;
use Thapp\Image\Metrics\Point;
use Thapp\Image\Metrics\Gravity;
use Thapp\Image\Tests\ImageTest as AbstractImageTest;

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

    protected $images = [];

    /** @test */
    public function itShouldGetImagick()
    {
        $image = $this->newImage(100, 100);
        $this->assertInstanceof('Gmagick', $image->getGmagick());
    }

    /** @test */
    public function gmagickShouldBeSwapable()
    {
        $image = $this->newImage(100, 100);
        $image->swapGmagick($gmagick = new \Gmagick);

        $this->assertSame($gmagick, $image->getGmagick());
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

        return $this->images[] = $image;
    }

    protected function getDriverName()
    {
        return 'gmagick';
    }

    protected function newImage($w, $h, $format = 'jpeg')
    {
        $resource = $this->getTestImage($w, $h, $format);
        $source = new Source;

        $meta = stream_get_meta_data($resource);
        $file = $meta['uri'];


        return $this->images[] = $source->load($file);
    }

    protected function setUp()
    {
        if (!class_exists('Gmagick')) {
            $this->markTestIncomplete();
        }

        foreach ($this->images as $image) {
            $image->destroy();
        }

        $this->images = [];

        parent::setUp();
    }

    protected function tearDown()
    {
        if (is_resource($this->handle)) {
            fclose($this->handle);
        }

        // fix segmentation fault for Gmagick
        foreach ($this->images as $image) {
            $image->destroy();
        }

        $this->images = [];
    }
}
