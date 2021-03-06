<?php

/*
 * This File is part of the Thapp\Image\Filter\Gd package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Filter\Gd;

use Thapp\Image\Driver\ImageInterface;
use Thapp\Image\Filter\HslHelperTrait;
use Thapp\Image\Filter\FilterHelperTrait;

/**
 * @class Hue
 *
 * @package Thapp\Image\Filter\Gd
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Modulate extends GdFilter
{
    use HslHelperTrait;

    private $degree;
    private $saturation;
    private $brightness;

    /**
     * Constructor.
     *
     * @param float $angle
     * @param float $angle
     */
    public function __construct($brightness = 100, $saturation = 100, $hue = 100)
    {
        $this->setDegree($hue);
        $this->setBrightness($brightness);
        $this->setSaturation($saturation);
    }

    /**
     * {@inheritdoc}
     */
    public function apply(ImageInterface $image)
    {
        $gd = $image->getGd();

        //if (null !== $this->brightness) {
        //    imagefilter($gd, IMG_FILTER_BRIGHTNESS, $this->brightness);
        //}

        if (0 !== $this->saturation && 0 !== $this->degree) {
            $img = $this->applyHue($gd, $image->getWidth(), $image->getHeight(), $this->degree, $this->saturation, $this->brightness);

            if (null !== $img && $img !== $gd) {
                $image->swapGd($img);
            }
        }

    }

    /**
     * applyHue
     *
     * @param resource $gd
     * @param int $width
     * @param int $height
     * @param float $angle
     * @param float $saturation
     *
     * @return void
     */
    protected function applyHue($gd, $width, $height, $angle, $saturation, $brightness)
    {
        $noHue = 0 === $angle % 360;

        if ($noHue && 1.0 === $saturation) {
            return;
        }

        //var_dump($angle);
        //die;

        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $index = imagecolorat($gd, $x, $y);
                list ($h, $s, $l) = $this->rgbToHsl(($index >> 16) & 0xFF, ($index >> 8) & 0xFF, $index & 0xFF);

                //$h = $this->setHue($h, $angle, $noHue);
                //
                $h += $angle;

                if (360 < $h) {
                    $h -= 360;
                }

                list($r, $g, $b) = $this->hslToRgb($h, max(0, min(1, $s * $saturation)), max(0, min(1, $brightness * $l)));

                imagesetpixel($gd, $x, $y, imagecolorallocatealpha($gd, $r, $g, $b, ($index & 0x7F000000) >> 24));
            }
        }
    }

    /**
     * getHue
     *
     * @param float   $h
     * @param float   $angle
     * @param boolean $noChange
     *
     * @return void
     */
    private function setHue($h, $angle, $noChange) {

        //if ($noChange) {
        //    return $h;
        //}

        $h += $angle / 360;

        if ($h > 1) {
          $h--;
        }

        return $h;
    }

    private function setSaturation($val)
    {
        $this->getVal($val);
        $this->saturation = $val / 100;

    }

    private function setBrightness($val)
    {
        $this->getVal($val);
        $this->brightness = $val / 100;
        //$this->brightness = (-1 + ($val / 100)) * 255;
    }

    private function getVal(&$val)
    {
        $val = (float)min(200, max(0, $val));
    }

    private function setDegree($val)
    {
        $this->getVal($val);

        $deg = 180 - 3.6 * (0.5 * $val);

        while ($deg < 0) {
            $deg += 360;
        }

        $this->degree = 360 - $deg;
    }
}
