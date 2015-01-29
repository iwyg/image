<?php

/*
 * This File is part of the Thapp\Image\Tests package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Tests\Driver;

use Imagick;
use Thapp\Image\Driver\ImageInterface;

/**
 * @trait ImageTestHelper
 *
 * @package Thapp\Image\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
trait ImageTestHelper
{
    protected $assets;

    protected function getTestImage($w = 100, $h = 100, $format = 'jpeg')
    {
        $stream = tmpfile();
        $resource = imagecreatetruecolor($w, $h);

        $fn = 'image'.$format;

        ob_start();
        call_user_func($fn, $resource);

        fwrite($stream, ob_get_clean());
        rewind($stream);

        return $stream;
    }

    protected function asset($file)
    {
        return dirname(__DIR__).'/Fixures/'.$file;
    }

    protected function assertTransparent(ImageInterface $image, $transparent = true, $x = 0, $y = 0)
    {
        $alpha = (imagecolorat($image->getGd(), $x, $y) & 0x7F000000) >> 24;

        return $transparent ? $alpha > 0 : 0 === $alpha;
    }

    protected function assertAlphaChannelGmagick(ImageInterface $image, $alpha)
    {
        $gmagick = new Gmagick;
        $gmagick->readImageBlob($image->getBlob());
        $channel = $gmagick->getImageAlphaChannel();
        $gmagick->clear();
        $gmagick->destroy();

        $this->assertSame($alpha, $channel);
    }

    protected function assertAlphaChannelImagick(ImageInterface $image, $alpha)
    {
        $imagick = new Imagick;
        $imagick->readImageBlob($image->getBlob());
        $channel = $imagick->getImageAlphaChannel();
        $imagick->clear();
        $imagick->destroy();

        $this->assertSame($alpha, $channel);
    }

    protected function mockImage($class = 'Thapp\Image\Driver\Gd\Image')
    {
        return $this->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
