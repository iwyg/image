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

    //protected function setUp()
    //{
    //    if (isset($_ENV['IMAGE_DRIVER']) && 'gd' !== $_ENV['IMAGE_DRIVER']) {
    //        $this->markTestIncomplete();
    //    }

    //    parent::setUp();
    //}
}
