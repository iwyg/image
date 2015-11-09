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
    protected $streams = [];

    protected function getTestImage($w = 100, $h = 100, $format = 'jpeg')
    {
        //$f = tempnam(sys_get_temp_dir(), 'img_'.(string)microtime(true));
        //$stream = fopen($f, 'rw+'$f, 'rw+');
        $this->streams[] = $stream = tmpfile();
        $meta = stream_get_meta_data($stream);
        $path = $meta['uri'];

        $resource = imagecreatetruecolor($w, $h);

        if (!function_exists($fn = 'image'.$format)) {
            throw new \RuntimeException(sprintf('Cannot create test image of type %s.', $fromat));
        }

        ob_start();
        call_user_func($fn, $resource, $path);

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

    protected function assertTransparentGmagick(ImageInterface $image, $transparent = true, $alpha, $x = 0, $y = 0)
    {
        $gd = imagecreatefromstring($image->getBlob());

        $alpha = (imagecolorat($gd, $x, $y) & 0x7F000000) >> 24;

        imagedestroy($gd);

        return $transparent ? $alpha > 0 : 0 === $alpha;

        //$gmagick = new Gmagick;
        //$gmagick->readImageBlob($image->getBlob());
        //$channel = $gmagick->getImageAlphaChannel();
        //$gmagick->clear();
        //$gmagick->destroy();

        //$this->assertSame($alpha, $channel);
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