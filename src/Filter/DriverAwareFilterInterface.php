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

use Thapp\Image\Driver\ImageInterface;

/**
 * @interface DriverAwareFilterInterface
 *
 * @package Thapp\Image\Filter
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface DriverAwareFilterInterface extends FilterInterface
{
    /**
     * Tell if given image driver is supported by this driver Supports
     *
     * @param ImageInterface $image
     *
     * @return boolean
     */
    public function supports(ImageInterface $image);
}
