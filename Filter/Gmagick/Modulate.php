<?php

/*
 * This File is part of the Thapp\JitImage\Filter package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Filter\Imagick;

use Thapp\Image\Driver\ImageInterface;
use Thapp\Image\Filter\AbstractModulate;

/**
 * @class Greyscale
 *
 * @package Thapp\JitImage\Filter
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Modulate extends GmagickFilter
{
    use AbstractModulate;

    protected function applyModulate(ImageInterface $image, $brightnes, $saturation, $hue)
    {
        $gmagick = $image->getGmagick();
        $gmagick->modulateImage($brightnes, $saturation, $hue);
    }
}
