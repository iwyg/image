<?php

/*
 * This File is part of the Thapp\Image\Tests\Driver\Imagick package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Driver\Gd;

use Imagick;
use Thapp\Image\Geometry\Size;
use Thapp\Image\Geometry\Point;
use Thapp\Image\Driver\Gd\Image;
use Thapp\Image\Driver\Gd\Source;
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

    /** @test */
    public function itShouldNotHaveFrames()
    {
        $image = $this->newImage(100, 100);
        $this->assertFalse($image->hasFrames());
    }

    /** @test */
    public function itShouldErrorWhenInjectingPaletteImages()
    {
        $gd = imagecreate(200, 200);
        try {
            $image = new Image($gd, $this->mockPalette());
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Thapp\Image\Driver\Gd\Image only supports truecolor images.', $e->getMessage());
            return;
        }

        $this->fail();
    }

    /** @test */
    public function itShouldLoadRemoteImage()
    {
        $url = 'http://upload.wikimedia.org/wikipedia/commons/4/4a/Logo_2013_Google.png';

        $stream = fopen($url, 'r');

        $src = new Source;
        $image = $src->read($stream);

        $data = $image->getMetaData()->all();
        $this->assertTrue(!empty($data));

        fclose($stream);
    }

    protected function mockPalette()
    {
        return $this->getMockBuilder('Thapp\Image\Color\Palette\Rgb')
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function getDriverName()
    {
        return 'gd';
    }

    protected function loadImage($file)
    {
        return (new Source())->load($file);
    }

    protected function newImage($w, $h, $format = 'jpeg')
    {
        $resource = $this->getTestImage($w, $h, $format);
        $source = new Source;

        return $source->read($resource);
    }

    protected function setUp()
    {
        if (isset($_ENV['IMAGE_DRIVER']) && 'gd' !== $_ENV['IMAGE_DRIVER']) {
            $this->markTestIncomplete();
        }

        parent::setUp();
    }
}
