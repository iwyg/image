<?php

/**
 * This File is part of the Thapp\Image package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Filter\Overlay;

use \Imagick;
use \ImagickPixel;
use Thapp\Image\Filter\ImagickFilter;

/**
 * @class ImagickOverlayFilter
 * @package Thapp\Image
 * @version $Id$
 */
class ImagickOverlayFilter extends ImagickFilter
{
    protected $availableOptions = ['c', 'a'];

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        extract($this->driver->getTargetSize());

        $image = $this->driver->getResource();

        $rgba    = implode(',', $this->hexToRgb($this->getOption('c', 'fff')));
        $alpha   = $this->getOption('a', '0.5');

        $overlay = new Imagick();
        $overlay->newImage($width, $height, new ImagickPixel(sprintf('rgba(%s,%s)', $rgba, $alpha)));
        $image->compositeImage($overlay, Imagick::COMPOSITE_OVER, 0, 0);
        //$this->driver->swapResource($overlay);
    }
}
