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

/**
 * @trait ImageTestHelper
 *
 * @package Thapp\Image\Tests
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
trait ImageTestHelper
{
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
}
