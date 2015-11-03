<?php

/*
 * This File is part of the Thapp\Image\Filter package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Filter;

/**
 * @class AbstractHue
 *
 * @package Thapp\Image\Filter
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
trait HslHelperTrait
{
    /**
     * rgbToHsl
     *
     * @param int $red
     * @param int $green
     * @param int $blue
     *
     * @return array
     */
    public function rgbToHsl($r, $g, $b)
    {
        $r /= 255;
        $g /= 255;
        $b /= 255;

        $cMax = max($r, $g, $b);
        $cMin = min($r, $g, $b);

        $l = ($cMax + $cMin) / 2;

        if ($cMax === $cMin) {
            return [0, 0, $l];
        }

        $delta = $cMax - $cMin;

        switch ($cMax) {
            case $r:
                $h = 0 > ($h1 = ($g - $b) / $delta) ? $h1 + 6 : fmod($h1, 6);
                break;
            case $g:
                $h = ($b - $r) / $delta + 2;
                break;
            case $b:
                $h = ($r - $g) / $delta + 4;
                break;
        }

        $h = round($h * 60);
        $s = 0 === $delta ? 0 : $delta / (1 - abs(2 * $l - 1));
        //$s = $l > 0.5 ? $delta / (2 - $cMax - $cMin) : $delta / ($cMax + $cMin);

        return [$h, $s, $l];
    }

    /**
     * hslToRgb
     *
     * @param float $h
     * @param float $s
     * @param float $l
     *
     * @return array
     */
    public function hslToRgb($h, $s, $l)
    {
        //$h /= 100;
        if (0 === $s) {
            $r = $g = $b = (int)round($l * 255);
            return [$r, $g, $b];
        }

        $c = (1 - abs(2 * $l - 1)) * $s;
        $x = $c * (1 - abs(fmod(($h / 60), 2) - 1));
        $m = $l - $c / 2;

        if ($h < 60) {
            $rgb = [$c, $x, 0];
        } elseif ($h < 120) {
            $rgb = [$x, $c, 0];
        } elseif ($h < 180) {
            $rgb = [0, $c, $x];
        } elseif ($h < 240) {
            $rgb = [0, $x, $c];
        } elseif ($h < 300) {
            $rgb = [$x, 0, $c];
        } else {
            $rgb = [$c, 0, $x];
        }

        return [(int)round(255 * ($rgb[0] + $m)), (int)round(255 * ($rgb[1] + $m)), (int)round(255 * ($rgb[2] + $m))];
    }
}
