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

/**
 * @trait GdFilterTrait
 *
 * @package Thapp\Image\Filter\Gd
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
trait GdFilterTrait
{
    public function supports(ImageInterface $image)
    {
        return $image instanceof \Thapp\Image\Driver\Gd\Image;
    }
}
