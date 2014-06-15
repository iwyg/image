<?php

/**
 * This File is part of the Thapp\JitImage package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Filter\GreyScale;

use Thapp\Image\Filter\GdFilter;

/**
 * @class GdGreyscaleFilter
 * @package Thapp\Image
 * @version $Id$
 */
class GdGreyscaleFilter extends GdFilter
{

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        imagefilter($this->driver->getResource(), IMG_FILTER_GRAYSCALE);
    }
}
