<?php

/*
 * This File is part of the Thapp\Image\Driver package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Driver;

/**
 * @trait MagickHelper
 *
 * @package Thapp\Image\Driver
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
trait MagickHelper
{
    private static $filterKeys = [
        ImageInterface::FILTER_UNDEFINED, ImageInterface::FILTER_POINT,    ImageInterface::FILTER_BOX,
        ImageInterface::FILTER_TRIANGLE,  ImageInterface::FILTER_HERMITE,  ImageInterface::FILTER_HANNING,
        ImageInterface::FILTER_HAMMING,   ImageInterface::FILTER_BLACKMAN, ImageInterface::FILTER_GAUSSIAN,
        ImageInterface::FILTER_QUADRATIC, ImageInterface::FILTER_CUBIC,    ImageInterface::FILTER_CATROM,
        ImageInterface::FILTER_MITCHELL,  ImageInterface::FILTER_LANCZOS,  ImageInterface::FILTER_BESSEL,
        ImageInterface::FILTER_SINC
    ];

    /**
     * &filterMap
     *
     * @return array
     */
    private function &filterMap()
    {
        if (null === static::$filterMap) {
            static::$filterMap = array_combine(static::$filterKeys, $this->getMagickFilters());
        }

        return static::$filterMap;
    }

    abstract protected function getMagickFilters();
}
