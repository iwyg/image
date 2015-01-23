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
    /**
     * toRgb
     *
     * @param mixed $color
     *
     * @return array
     */
    public static function toRgb($color)
    {
        $channels = null;
        if (is_array($color)) {
            $channels = list($r, $g, $b, $a) = array_pad($color, 4, null);
        } elseif (is_string($color)) {
            if (preg_match('#^s?rgba?\((.*)\)$#', strtolower($color), $matches)) {
                $channels = list($r, $g, $b, $a) = array_map(function ($val) {
                    return 0 !== substr_count($val, '.') ? (float)$val : (int)$val;
                }, array_pad(preg_split('~,\s?~', $matches[1], -1, PREG_SPLIT_NO_EMPTY), 4, null));
            } elseif (static::isHex($color)) {
                $channels = list($r, $g, $b) = array_pad(static::hexToRgb($color), 4, null);
            }
        }

        if (null === $channels) {
            throw new \RuntimeException;
        }

        if (null === $channels[3]) {
            $channels[3] = 1.0;
        }

        return $channels;
    }

    /**
     * hexToRgb
     *
     * @param string $hex
     *
     * @return array
     */
    public static function hexToRgb($hex)
    {
        if (!static::isHex($color = ltrim($hex, '#'))) {
            throw new \InvalidArgumentException;
        }

        if (3 === ($len = strlen($color))) {
            list($r, $g, $b) = str_split($color);
            $rgb = [hexdec($r.$r), hexdec($g.$g), hexdec($b.$b)];
        } elseif (6 === $len) {
            list($r, $g, $b) = str_split($color, 2);
            $rgb = [hexdec($r), hexdec($g), hexdec($b)];
        }

        return $rgb;
    }

    /**
     * rgbToHex
     *
     * @param int $r
     * @param int $g
     * @param int $b
     *
     * @return string
     */
    public static function rgbToHex($r, $g, $b)
    {
        return sprintf('%02x%02x%02x', $r, $g, $b);
    }

    /**
     * isHex
     *
     * @param string $color
     *
     * @return boolean
     */
    public static function isHex($color)
    {
        return (boolean)preg_match('#^([[:xdigit:]]{6}|[[:xdigit:]]{3})$#', ltrim($color, '#'));
    }

    /**
     * normalize
     *
     * @param string $hex
     *
     * @return string
     */
    public static function normalize($hex)
    {
        if (3 === strlen($color = ltrim($hex, '#'))) {
            list ($r, $g, $b) = str_split($color);

            return '#'.$r.$r.$g.$g.$b.$b;
        }

        return '#'.$color;
    }
}


