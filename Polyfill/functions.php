<?php

// php < 5.5
if (!function_exists('imagepalettetotruecolor')) {

    /**
     * Converts a palette based image to true color
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
