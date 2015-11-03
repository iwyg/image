<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Filter\Im;

use Thapp\Image\Driver\ImageInterface;
use Thapp\Image\Filter\AbstractModulate;
use Thapp\Image\Driver\Im\Command\Raw;

/**
 * @class Greyscale
 *
 * @package Thapp\JitImage\Filter
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Modulate extends ImagickFilter
{
    use AbstractModulate;

    protected function applyModulate(ImageInterface $image, $brightnes, $saturation, $hue)
    {
        $image->addCommand(new Raw(sprintf('-modulate %s%%,%s%%,%s%%', $brightnes, $saturation, $hue)));
    }
}
