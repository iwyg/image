<?php

/**
 * This File is part of the Thapp\Image package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Filter\Traits;

/**
 * @trait FilterHelperTrait
 *
 * @package Thapp\Image
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
trait FilterHelperTrait
{
    public function hexToRgb($hex)
    {
        if (3 === ($len = strlen($hex))) {
            $rgb = str_split($hex);
            list($r, $g, $b) = $rgb;
            $rgb = [hexdec($r.$r), hexdec($g.$g), hexdec($b.$b)];
        } elseif (6 === $len) {
            $rgb = str_split($hex, 2);
            list($r, $g, $b) = $rgb;
            $rgb = [hexdec($r), hexdec($g), hexdec($b)];
        } else {
            throw new \InvalidArgumentException(sprintf('invalid hex value %s', $hex));
        }

        return $rgb;
    }
}
