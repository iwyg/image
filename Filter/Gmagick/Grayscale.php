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

use Gmagick;
use Thapp\Image\Driver\ImageInterface;

/**
 * @class Grayscale
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Grayscale extends GmagickFilter
{
    /**
     * {@inheritdoc}
     */
    public function apply(ImageInterface $image)
    {
        $image->getGmagick()->modulateImage(100, 0, 100);
    }
}
