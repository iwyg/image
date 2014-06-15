<?php

/**
 * This File is part of the Thapp\Image package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Filter\Colorize;

use \Thapp\Image\Filter\GdFilter;

/**
 * @class GdColorizeFilter
 * @package Thapp\Image
 * @version $Id$
 */
class GdColorizeFilter extends GdFilter
{
    protected $availableOptions = ['c'];

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        list($r, $g, $b) = $this->hexToRgb($this->getOption('c', 'fff'));

        imagefilter($this->driver->getResource(), IMG_FILTER_CONTRAST, 1);
        imagefilter($this->driver->getResource(), IMG_FILTER_BRIGHTNESS, -12);
        imagefilter($this->driver->getResource(), IMG_FILTER_GRAYSCALE);

        $this->createOverly($r, $g, $b);
    }

    /**
     * Create the image overlay
     *
     * @param string $r Red channel
     * @param string $g Green channel
     * @param string $b Blue channel
     *
     * @return void
     */
    private function createOverlay($r, $g, $b)
    {
        extract($this->driver->getTargetSize());

        $overlay = imagecreatetruecolor($width, $height);

        imagealphablending($image = $this->driver->getResource(), true);
        imagelayereffect($image, IMG_EFFECT_OVERLAY);
        imagefilledrectangle($overlay, 0, 0, $width, $height, imagecolorallocatealpha($overlay, $r, $g, $b, 0));
        imagecopy($image, $overlay, 0, 0, 0, 0, imagesx($overlay), imagesy($overlay));
    }
}
