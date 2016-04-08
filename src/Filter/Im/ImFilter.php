<?php

/*
 * This File is part of the Thapp\Image package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Thapp\Image\Filter\Im;

use Thapp\Image\Driver\ImageInterface;
use Thapp\Image\Filter\DriverAwareFilterInterface;

/**
 * @class ImFilter
 *
 * @package Thapp\Image
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class ImFilter implements DriverAwareFilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(ImageInterface $image)
    {
        return $image instanceof \Thapp\Image\Driver\Im\Image;
    }
}
