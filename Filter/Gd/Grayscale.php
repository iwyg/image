<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Filter\Gd;

use Thapp\Image\Driver\ImageInterface;

/**
 * @class Grayscale
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Grayscale extends GdFilter
{
    /**
     * {@inheritdoc}
     */
    public function apply(ImageInterface $image)
    {
        imagefilter($image->getGd(), IMG_FILTER_GRAYSCALE);
    }
}
