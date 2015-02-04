<?php

/*
 * This File is part of the Thapp\Image\Filter\Imagick package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Filter\Imagick;

use Imagick;
use Thapp\Image\Driver\ImageInterface;

/**
 * @class Grayscale
 *
 * @package Thapp\Image\Filter\Imagick
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Grayscale extends ImagickFilter
{
    /**
     * {@inheritdoc}
     */
    public function apply(ImageInterface $image)
    {
        $image->getImagick()->modulateImage(100, 0, 100);
    }
}
