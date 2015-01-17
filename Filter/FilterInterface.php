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
 * @interface FilterInterface
 *
 * @package Thapp\Image\Filter
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface FilterInterface
{
    public function apply(ImageInterface $image);
}
