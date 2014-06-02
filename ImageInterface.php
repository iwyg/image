<?php

/**
 * This File is part of the Thapp\Image package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image;

/**
 * @class ImageInterface
 * @package Thapp\Image
 * @version $Id$
 */
interface ImageInterface
{
    const DRIVER_IM = 'im';

    const DRIVER_IMAGICK = 'imagick';

    const DRIVER_GD = 'gd';
}
