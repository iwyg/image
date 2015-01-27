<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */


namespace Thapp\Image\Filter\Gmagick;

use Thapp\Image\Filter\Filter;
use Thapp\Image\Driver\ImageInterface;

/**
 * @class GmagickFilter
 * @see Filter
 * @abstract
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class ImagickFilter extends Filter
{
    /**
     * {@inheritdoc}
     */
    public function supports(ImageInterface $image)
    {
        return $image instanceof Thapp\Image\Driver\Gmagick\Image;
    }
}
