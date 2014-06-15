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

use \Imagick;
use \ImagickPixel;
use \Thapp\Image\Filter\ImagickFilter;

/**
 * @class ImagickColorizeFilter
 * @package Thapp\Image
 * @version $Id$
 */
class ImagickColorizeFilter extends ImagickFilter
{
    protected $availableOptions = ['c'];

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        extract($this->driver->getTargetSize());

        $overlay = new Imagick();
        $overlay->newImage(
            $width,
            $height,
            new ImagickPixel(sprintf('rgb(%s)', implode(',', $this->hexToRgb($this->getOption('c', 'fff')))))
        );

        $this->driver->getResource()->compositeImage($overlay, Imagick::COMPOSITE_COLORIZE, 0, 0);
    }
}
