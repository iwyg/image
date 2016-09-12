<?php

/*
 * This File is part of the Thapp\Image package
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
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
final class Parser
{
    /**
     * Parses a color to Rgb
     *
     * @param mixed $color A string in rgb, or hex notation, an array of color
     * values.
     *
     * @return array
     */
    public static function toRgb($color)
    {
        $colors = static::parse($color);

        if (array_key_exists(ColorInterface::CHANNEL_KEY, $colors)) {
            return static::fourCToRgb($colors);
        }

        return static::mapRgb($colors);
    }

    /**
     * Parses a color to greyscale
     *
     * @see Parser::toRgb()
     *
     * @return array
     */
    public static function toGrayscale($color)
    {
        $colors = $c = static::parse($color);

        if (array_key_exists(ColorInterface::CHANNEL_KEY, $colors)) {
            $rgb = static::fourCToRgb($colors);
            $colors = [$rgb[0], 1.0];
        }

        $alpha = array_pop($colors);

        if (1 !== count($colors = array_unique(array_values($colors)))) {
            throw new \InvalidArgumentException(
                sprintf('Invalid color set %s for grayscale conversion.', json_encode(array_values($c)))
            );
        }

        return [(int)$colors[0], (float)$alpha];
    }

    /**
     * @see Parse::toCmyk()
     */
    public static function to4c($color)
    {
        return static::toCmyk($color);
    }

    /**
     * Parses a color to Cmyk
     *
     * @see Parser::toRgb()
     *
     * @return void
     */
    public static function toCmyk($color)
    {
        $colors = static::parse($color);


        if (!array_key_exists(ColorInterface::CHANNEL_KEY, $colors)) {
            $r = ($colors[ColorInterface::CHANNEL_RED] / 255);
            $g = ($colors[ColorInterface::CHANNEL_GREEN] / 255);
            $b = ($colors[ColorInterface::CHANNEL_BLUE] / 255);
            $k = (float)(1 - max($r, $g, $b));

            // transform the alpha values to saturation of 4c colors.
            $a = $colors[ColorInterface::CHANNEL_ALPHA];

            $colors = array_map(function ($c) use ($k) {
                return 1.0 === $k ? 0 : round(((1 - $c - $k) / (1 - $k)), 2);
            }, [$r, $g, $b]);
            $colors[] = round($k, 2);//round($a * ($k * 100), 2)
        }

        return static::map4c($colors);
    }

    /**
     * Takes a 16 or 8 bit hex string and parses it to rgb.
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
     * Takes rgb values and converts them to a hex string.
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
     * Checks if the input color is a valid hex color string.
     *
     * @param string $color
     *
     * @return boolean
     */
    public static function isHex($color)
    {
        return ctype_xdigit($color = ltrim($color, '#')) && in_array(strlen($color), [3, 6]);
    }

    /**
     * Converts a 8bit hex string to 16 bit notation.
     *
     * @param string $hex
     *
     * @return string
     */
    public static function normalizeHex($hex)
    {
        if (3 === strlen($color = ltrim($hex, '#'))) {
            list($r, $g, $b) = str_split($color);

            return '#'.$r.$r.$g.$g.$b.$b;
        }

        return '#'.substr($color, 0, 6);
    }

    /**
     * Converts CMYK values to RGB values.
     *
     * @param float $c Cyan value from 0 to 100
     * @param float $m Magenta value from 0 to 100
     * @param float $y Yellow value from 0 to 100
     * @param float $k Black value from 0 to 100
     *
     * @return array
     */
    public static function cmykToRgb($c, $m, $y, $k)
    {
        $k = 1 - $k / 100;
        
        return array_map(function ($c) use ($k) {
            return min(255, max(0, (int)round(255 * (1 - $c / 100) * $k)));
        }, [$c, $m, $y]);
    }

    /**
     * Parses the input color value
     *
     * @param mixed $color
     *
     * @return array
     */
    private static function parse($color)
    {
        if (is_array($color)) {
            if (0 === ($count = count($color)) || 4 < $count) {
                throw new \InvalidArgumentException(sprintf('Invalid argument value %s.', json_encode($color)));
            }

            if (3 > $count) {
                list($g, $a) = array_pad(array_values($color), 2, 1.0);
                $color = [$g, $g, $g, $a];
            } elseif (4 === $count && Cmyk::keys() === array_keys($color)) {
                return $color;
            }

            return array_combine(Rgb::keys(), array_pad(array_values($color), 4, 1.0));
        }

        // case integer:
        // E.g. returned by imageallocatecolor
        if (is_int($color)) {
            return array_combine(Rgb::keys(), [
                ($color >> 16) & 0xFF,
                ($color >> 8) & 0xFF,
                $color & 0xFF,
                1 - round(((($color & 0x7F000000) >> 24) / 127), 2)
            ]);
        } elseif (!is_string($color)) {
            throw new \InvalidArgumentException(sprintf('Unsupported argument value of type %s.', gettype($color)));
        }

        // case hex:
        if (static::isHex($color)) {
            return array_combine(Rgb::keys(), array_pad(static::hexToRgb($color), 4, 1.0));
        }

        // case matches rgb():
        if (preg_match('#^s?rgba?\((.*)\)$#', strtolower($color), $matches)) {
            if (3 > count($parts = preg_split('~,\s?~', $matches[1], -1, PREG_SPLIT_NO_EMPTY))) {
                throw new \InvalidArgumentException(sprintf('Invalid rgb definition "%s".', $color));
            }

            $colors = array_map(function ($val) {
                return (float)$val;//is_string($val) && 0 !== substr_count($val, '.') ? (float)$val : (int)$val;
            }, array_pad($parts, 4, 1.0));

            return array_combine(Rgb::keys(), $colors);
        }

        // case cmyk()
        if (0 === strpos($color, 'cmyk(')) {
            $cmyk = array_map(function ($val) {
                $ret = is_string($val) ? trim($val, ' %') : (string)$val;
                return is_numeric($ret) ? (float)$ret : $ret;
            }, array_pad(explode(',', substr($color, 5, -1)), 4, null));

            if (!ctype_digit(implode('', $cmyk))) {
                throw new \InvalidArgumentException(sprintf('Invalid cmyk definition "%s".', $color));
            }

            return array_combine(Cmyk::keys(), $cmyk);
        }

        throw new \InvalidArgumentException(sprintf('Invalid color definition "%s".', (string)$color));
    }

    private static function fourCToRgb(array $cmyk)
    {
        list($c, $m, $y, $k) = array_values($cmyk);

        return static::cmykToRgb($c, $m, $y, $k);
    }

    private static function mapRgb(array $colors, $alpha = null)
    {
        $alpha = array_pop($colors);

        $colors = array_map(function ($color) {
            return (int)$color;
        }, array_values($colors));
        $colors[] = null !== $alpha ? (float)$alpha : null;

        return $colors;
    }

    private static function map4c(array $colors)
    {
        return array_map(function ($color) {
            return (int)($color * 100);
        }, array_values($colors));
    }

    private function __construct()
    {
    }
}
