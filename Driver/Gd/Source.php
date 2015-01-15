<?php

/*
 * This File is part of the Thapp\Image\Driver\Imagick package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Gd;

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
    /**
     * {@inheritdoc}
     */
    public function read($resource)
    {
        rewind($resource);

        return $this->create(stream_get_contents($resource));
    }

    public function load($file)
    {
        if ($gd = Image::gdCreateFromFile($file)) {
            return new Image($gd);
        }

        return false;
    }

    public function create($image)
    {
        return new Image(imagecreatefromString($image));
    }
}
