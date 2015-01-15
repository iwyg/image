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
    /**
     * {@inheritdoc}
     */
    public function read($resource)
    {
        $imagick = new Imagick;

        try {
            $imagick->readImageFile($resource);
        } catch (\Exception $e) {
            $imagick->destroy();

            return false;
        }

        return new Image($imagick);

    }

    /**
     * {@inheritdoc}
     */
    public function load($file)
    {
        $imagick = new Imagick;

        try {
            $imagick->readImage($file);
        } catch (\Exception $e) {
            $imagick->destroy();

            return false;
        }

        return new Image($imagick);
    }

    /**
     * {@inheritdoc}
     */
    public function create($image)
    {
        $imagick = new Imagick;

        try {
            $imagick->readImageBlob($image);
        } catch (\Exception $e) {
            $imagick->destroy();

            return false;
        }

        return new Image($imagick);
    }
}
