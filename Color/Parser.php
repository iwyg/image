<?php

/*
 * This File is part of the Thapp\Image\Color package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Color;

/**
 * @class Parser
 *
 * @package Thapp\Image\Color
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Parser
{
    public static function hexToRgb($hex)
    {
        if (!static::isHex($color = ltrim($hex, '#'))) {
            throw new \InvalidArgumentException;
        }

        if (3 === ($len = strlen($hex))) {
            $rgb = str_split($hex);
            list($r, $g, $b) = $rgb;
            $rgb = [hexdec($r.$r), hexdec($g.$g), hexdec($b.$b)];
        } elseif (6 === $len) {
            $rgb = str_split($hex, 2);
            list($r, $g, $b) = $rgb;
            $rgb = [hexdec($r), hexdec($g), hexdec($b)];
        }

        return $rgb;
    }

    public static function rgbToHex($r, $g, $b)
    {
        $hex = '';

        foreach ([$r, $g, $b] as $channel) {
            $c = dechex($channel);
            $hex .= str_pad($c, 2, '0', STR_PAD_LEFT);
        }

        return $hex;
    }

    public static function isHex($color)
    {
        return (boolean)preg_match('#^([[:xdigit:]]{6}|[[:xdigit:]]{3})$#', $color);
    }

    public static function normalize($hex)
    {
        if (3 === strlen($color = ltrim($hex, '#'))) {
            list ($r, $g, $b) = str_split($color);

            return '#'.$r.$r.$g.$g.$b.$b;
        }

        return $hex;
    }
}


