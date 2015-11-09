<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

if (!function_exists('imagepalettetotruecolor')) {
    /**
     * Converts a palette based image to true color
     *
     * Used for PHP versions < 5.5
     *
     * @see http://php.net/manual/en/function.imagepalettetotruecolor.php
     *
     * @return boolean
     */
    function imagepalettetotruecolor(&$gd)
    {
        $truecolor = imagecreatetruecolor($w = imagesx($gd), $h = imagesy($gd));

        if (true !== imagecopy($truecolor, $gd, 0, 0, 0, 0, $w, $h)) {
            return false;
        }

        imagedestroy($gd);

        $gd = $truecolor;

        return true;
    }
}
