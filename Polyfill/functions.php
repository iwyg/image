<?php

// php >= 5.5
if (!function_exists('imagepalettetotruecolor')) {

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
