<?php

/*
 * This File is part of the Thapp\Image\Driver\Gd package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver\Gd;

use Thapp\Image\Color\RgbInterface;
use Thapp\Image\Color\ColorInterface;

/**
 * @trait GdHelper
 *
 * @package Thapp\Image\Driver\Gd
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
trait GdHelper
{
    /**
     * getColorId
     *
     * @param mixed $gd
     * @param ColorInterface $color
     *
     * @return void
     */
    private function getColorId($gd, ColorInterface $color = null)
    {
        if (null === $color) {
            return imagecolorallocate($gd, 255, 255, 255);
        }

        if (!$color instanceof RgbInterface) {
            throw new InvalidArgumentException('Image does only support RGB colors.');
        }

        $alpha = max(0, 127 - (int)round($color->getAlpha() * 127));

        return imagecolorallocatealpha($gd, $color->getRed(), $color->getGreen(), $color->getBlue(), $alpha);
    }

}
