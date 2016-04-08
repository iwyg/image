<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Filter\Imagick;

use Thapp\Image\Driver\ImageInterface;
use Thapp\Image\Filter\DriverAwareFilterInterface;

/**
 * @class ImagickFilter
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class ImagickFilter implements DriverAwareFilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(ImageInterface $image)
    {
        return $image instanceof \Thapp\Image\Driver\Imagick\Image;
    }
}
