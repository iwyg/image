<?php

/*
 * This File is part of the Thapp\Image\Driver\Imagick package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Imagick;

use Imagick;
use Thapp\Image\Driver\SourceInterface;

/**
 * @class Source
 *
 * @package Thapp\Image\Driver\Imagick
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Source implements SourceInterface
{
    public function read($resource)
    {
        $imagick = new Imagick;

        if ($imagick->readImageFile($resource)) {
            return new Image($imagick);
        }

        $imagick->destroy();

        return false;
    }

    public function load($file)
    {
        $imagick = new Imagick;

        if ($imagick->readImage($file)) {
            return new Image($imagick);
        }

        $imagick->destroy();

        return false;
    }

    public function create($image)
    {
        $imagick = new Imagick;

        if ($imagick->readImageBlob($image)) {
            return new Image($imagick);
        }

        $imagick->destroy();

        return false;
    }
}
